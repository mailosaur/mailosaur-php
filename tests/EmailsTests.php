<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\Message;
use Mailosaur\Models\MessageSummary;
use Mailosaur\Models\SearchCriteria;
use Mailosaur\Models\MessageCreateOptions;
use Mailosaur\Models\MessageForwardOptions;
use Mailosaur\Models\MessageReplyOptions;
use Mailosaur\Models\Attachment;

class EmailsTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient
     */
    protected static $client;

    /** @var string */
    protected static $server;

    /** @var string */
    protected static $verifiedDomain;

    /** @var \Mailosaur\Models\MessageSummary[] */
    protected static $emails;


    public static function setUpBeforeClass(): void
    {
        $baseUrl      = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey       = getenv('MAILOSAUR_API_KEY');
        self::$server = getenv('MAILOSAUR_SERVER');
        self::$verifiedDomain = getenv('MAILOSAUR_VERIFIED_DOMAIN');

        if (empty($apiKey) || empty(self::$server)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        self::$client = new MailosaurClient($apiKey, $baseUrl);

        self::$client->messages->deleteAll(self::$server);

        Mailer::sendEmails(self::$client, self::$server, 5);

        self::$emails = self::$client->messages->all(self::$server)->items;
    }

    public function testListWithReceivedAfter()
    {
        $pastDate = new \DateTime();
        $pastDate->sub(new \DateInterval('PT10M'));
        $pastEmails = self::$client->messages->all(self::$server, null, null, $pastDate)->items;
        $this->assertTrue(count($pastEmails) > 0);

        $futureDate = new \DateTime();
        $futureDate->add(new \DateInterval('PT1M'));
        $futureEmails = self::$client->messages->all(self::$server, null, null, $futureDate)->items;
        $this->assertCount(0, $futureEmails);
    }

    public function testGet()
    {
        $host             = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.net';
        $testEmailAddress = 'wait_for_test@' . self::$server . '.' . $host;

        Mailer::sendEmail(self::$client, self::$server, $testEmailAddress);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = $testEmailAddress;

        $email = self::$client->messages->get(self::$server, $criteria);

        $this->validateEmail($email);
    }

    public function testGetById()
    {
        $emailToRetrieve = self::$emails[0];

        $email = self::$client->messages->getById($emailToRetrieve->id);

        $this->validateEmail($email);
        $this->validateHeaders($email);
    }

    public function testGetByIdNotFound()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        self::$client->messages->getById(uniqid());
    }

    public function testSearchNoCriteriaError()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        self::$client->messages->search(self::$server, new SearchCriteria());
    }

    public function testSearchTimeoutErrorsSuppressed()
    {
        $criteria = new SearchCriteria();
        $criteria->sentFrom = 'neverfound@example.com';
        $results = self::$client->messages->search(self::$server, $criteria, 0, 1, 1, new \DateTime(), false)->items;
        $this->assertCount(0, $results);
    }

    public function testSearchBySentFrom()
    {
        $targetEmail = self::$emails[1];

        $criteria = new SearchCriteria();

        $criteria->sentFrom = $targetEmail->from[0]->email;

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->from[0]->email, $results[0]->from[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchBySentTo()
    {
        $targetEmail = self::$emails[1];

        $criteria = new SearchCriteria();

        $criteria->sentTo = $targetEmail->to[0]->email;

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchByBody()
    {
        $targetEmail    = self::$emails[1];
        $uniqueString   = substr($targetEmail->subject, 0, 10);
        $criteria       = new SearchCriteria();
        $criteria->body = $uniqueString . ' html';

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchBySubject()
    {
        $targetEmail       = self::$emails[1];
        $criteria          = new SearchCriteria();
        $criteria->subject = substr($targetEmail->subject, 0, 10);

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchMatchAll()
    {
        $targetEmail       = self::$emails[1];
        $criteria          = new SearchCriteria();
        $criteria->subject = substr($targetEmail->subject, 0, 10);
        $criteria->body    = 'this is a link';
        $criteria->match   = 'ALL';

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(1, $results);
    }

    public function testSearchMatchAny()
    {
        $targetEmail       = self::$emails[1];
        $criteria          = new SearchCriteria();
        $criteria->subject = substr($targetEmail->subject, 0, 10);
        $criteria->body    = 'this is a link';
        $criteria->match   = 'ANY';

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(6, $results);
    }

    public function testSearchWithSpecialCharacters()
    {
        $criteria          = new SearchCriteria();
        $criteria->subject = 'Search with ellipsis … and emoji 👨🏿‍🚒';

        $results = self::$client->messages->search(self::$server, $criteria)->items;

        $this->assertCount(0, $results);
    }

    public function testSpamAnalysis()
    {
        $targetId = self::$emails[0]->id;

        $result = self::$client->analysis->spam($targetId);

        foreach ($result->spamFilterResults->spamAssassin as $rule) {
            $this->assertNotEmpty($rule->rule);
            $this->assertNotEmpty($rule->description);
        }
    }

    public function testDelete()
    {
        $targetEmailId = self::$emails[4]->id;

        self::$client->messages->delete($targetEmailId);

        $this->expectException(\Mailosaur\Models\MailosaurException::class);

        self::$client->messages->delete($targetEmailId);
    }

    public function testAll()
    {
        foreach (self::$emails as $emailsummary) {
            $this->validateEmailSummary($emailsummary);
        }
    }

    public function testCreateSendText()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $subject = "New message";

        $options = new MessageCreateOptions();
        $options->to = 'anything@' . self::$verifiedDomain;
        $options->send = TRUE;
        $options->subject = $subject;
        $options->text = 'This is a new email';

        $message = self::$client->messages->create(self::$server, $options);

        $this->assertNotNull($message->id);
        $this->assertEquals($subject, $message->subject);
    }

    public function testCreateSendHtml()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $subject = "New HTML message";

        $options = new MessageCreateOptions();
        $options->to = 'anything@' . self::$verifiedDomain;
        $options->send = TRUE;
        $options->subject = $subject;
        $options->html = '<p>This is a new email.</p>';

        $message = self::$client->messages->create(self::$server, $options);

        $this->assertNotNull($message->id);
        $this->assertEquals($subject, $message->subject);
    }

    public function testCreateSendWithAttachment()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $subject = "New message with attachment";

        $options = new MessageCreateOptions();
        $options->to = 'anything@' . self::$verifiedDomain;
        $options->send = TRUE;
        $options->subject = $subject;
        $options->html = '<p>This is a new email.</p>';

        $data = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/cat.png');

        $attachment = new Attachment((object)[
            "fileName" => "cat.png",
            "content" => base64_encode($data),
            "contentType" => "image/png"
        ]);

        $options->attachments = [$attachment];

        $message = self::$client->messages->create(self::$server, $options);

        $this->assertCount(1, $message->attachments);
        $file1 = $message->attachments[0];
        $this->assertNotNull($file1->id);
        $this->assertEquals(82138, $file1->length);
        $this->assertNotNull($file1->url);
        $this->assertEquals('cat.png', $file1->fileName);
        $this->assertEquals('image/png', $file1->contentType);
    }

    public function testForwardText()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $body = "Forwarded message";
        $targetEmailId = self::$emails[0]->id;

        $options = new MessageForwardOptions();
        $options->to = 'anything@' . self::$verifiedDomain;
        $options->text = $body;

        $message = self::$client->messages->forward($targetEmailId, $options);

        $this->assertNotNull($message->id);
        $this->assertNotFalse(strpos($message->text->body, $body));
    }

    public function testForwardHtml()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $body = "<p>Forwarded <strong>HTML</strong> message.</p>";
        $targetEmailId = self::$emails[0]->id;

        $options = new MessageForwardOptions();
        $options->to = 'anything@' . self::$verifiedDomain;
        $options->html = $body;

        $message = self::$client->messages->forward($targetEmailId, $options);

        $this->assertNotNull($message->id);
        $this->assertNotFalse(strpos($message->html->body, $body));
    }

    public function testReplyText()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $body = "Reply message";
        $targetEmailId = self::$emails[0]->id;

        $options = new MessageReplyOptions();
        $options->text = $body;

        $message = self::$client->messages->reply($targetEmailId, $options);

        $this->assertNotNull($message->id);
        $this->assertNotFalse(strpos($message->text->body, $body));
    }

    public function testReplyHtml()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $body = "<p>Reply <strong>HTML</strong> message.</p>";
        $targetEmailId = self::$emails[0]->id;

        $options = new MessageReplyOptions();
        $options->html = $body;

        $message = self::$client->messages->reply($targetEmailId, $options);

        $this->assertNotNull($message->id);
        $this->assertNotFalse(strpos($message->html->body, $body));
    }

    public function testReplyWithAttachment()
    {
        if (empty(self::$verifiedDomain)) { $this->markTestSkipped(); }

        $body = "<p>Reply <strong>HTML</strong> message.</p>";
        $targetEmailId = self::$emails[0]->id;

        $options = new MessageReplyOptions();
        $options->html = $body;
        
        $data = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/cat.png');

        $attachment = new Attachment((object)[
            "fileName" => "cat.png",
            "content" => base64_encode($data),
            "contentType" => "image/png"
        ]);

        $options->attachments = [$attachment];

        $message = self::$client->messages->reply($targetEmailId, $options);

        $this->assertCount(1, $message->attachments);
        $file1 = $message->attachments[0];
        $this->assertNotNull($file1->id);
        $this->assertEquals(82138, $file1->length);
        $this->assertNotNull($file1->url);
        $this->assertEquals('cat.png', $file1->fileName);
        $this->assertEquals('image/png', $file1->contentType);
    }

    private function validateEmailSummary(MessageSummary $email)
    {
        $this->assertNotEmpty($email->summary);
        $this->assertEquals(2, $email->attachments);

        $email->received = $email->received->format(DATE_RFC2822);
        $email           = new Message($email);

        $this->validateMetadata($email);
    }


    private function validateEmail(Message $email)
    {
        $this->validateMetadata($email);
        $this->validateAttachmentMetadata($email);
        $this->validateHtml($email);
        $this->validateText($email);
    }

    private function validateMetadata(Message $email)
    {
        $this->assertEquals('Email', $email->type);
        $this->assertCount(1, $email->from);
        $this->assertCount(1, $email->to);
        $this->assertNotEmpty($email->from[0]->email);
        $this->assertNotEmpty($email->from[0]->name);
        $this->assertNotEmpty($email->to[0]->email);
        $this->assertNotEmpty($email->to[0]->name);
        $this->assertNotEmpty($email->subject);
        $this->assertEquals(date('d-m-Y'), $email->received->format('d-m-Y'));
    }

    private function validateAttachmentMetadata(Message $email)
    {
        $this->assertCount(2, $email->attachments);

        $file1 = $email->attachments[0];

        $this->assertNotNull($file1->id);
        $this->assertEquals(82138, $file1->length);
        $this->assertNotNull($file1->url);
        $this->assertEquals('ii_1435fadb31d523f6', $file1->fileName);
        $this->assertEquals('image/png', $file1->contentType);

        $file2 = $email->attachments[1];

        $this->assertNotNull($file2->id);
        $this->assertEquals(212080, $file2->length);
        $this->assertNotNull($file2->url);
        $this->assertEquals('Resources/dog.png', $file2->fileName);
        $this->assertEquals('image/png', $file2->contentType);
    }

    private function validateHtml(Message $email)
    {
        // html.body
        $this->assertStringStartsWith("<div dir=\"ltr\">", $email->html->body);

        // html.links
        $this->assertCount(3, $email->html->links);
        $this->assertEquals('https://mailosaur.com/', $email->html->links[0]->href);
        $this->assertEquals('mailosaur', $email->html->links[0]->text);
        $this->assertEquals('https://mailosaur.com/', $email->html->links[1]->href);
        $this->assertNull($email->html->links[1]->text);
        $this->assertEquals('http://invalid/', $email->html->links[2]->href);
        $this->assertEquals('invalid', $email->html->links[2]->text);

        // html.codes
        $this->assertCount(2, $email->html->codes);
        $this->assertEquals('123456', $email->html->codes[0]->value);
        $this->assertEquals('G3H1Y2', $email->html->codes[1]->value);

        // html.images
        $this->assertStringStartsWith('cid:', $email->html->images[1]->src);
        $this->assertEquals('Inline image 1', $email->html->images[1]->alt);
    }

    private function validateText(Message $email)
    {
        // text.body
        $this->assertStringStartsWith('this is a test', $email->text->body);

        // text.links
        $this->assertCount(2, $email->text->links);
        $this->assertEquals('https://mailosaur.com/', $email->text->links[0]->href);
        $this->assertEquals($email->text->links[0]->href, $email->text->links[0]->text);
        $this->assertEquals('https://mailosaur.com/', $email->text->links[1]->href);
        $this->assertEquals($email->text->links[1]->href, $email->text->links[1]->text);

        // text.codes
        $this->assertCount(2, $email->text->codes);
        $this->assertEquals('654321', $email->text->codes[0]->value);
        $this->assertEquals('5H0Y2', $email->text->codes[1]->value);
    }

    private function validateHeaders(Message $email)
    {
        $expectedFromHeader = $email->from[0]->name . ' <' . $email->from[0]->email . '>';
        $expectedToHeader   = $email->to[0]->name . ' <' . $email->to[0]->email . '>';

        // Fallback casing is used, as header casing is determined by sending server
        $this->assertEquals($expectedFromHeader, $this->getEmailHeaderValue($email, 'From'));
        $this->assertEquals($expectedToHeader, $this->getEmailHeaderValue($email, 'To'));
        $this->assertEquals($email->subject, $this->getEmailHeaderValue($email, 'Subject'));
    }

    /**
     * @param \Mailosaur\Models\Message $email
     * @param string                    $field
     *
     * @return string
     */
    private function getEmailHeaderValue(Message $email, $field)
    {
        $fallbackValue = '';

        foreach ($email->metadata->headers as $header) {
            if ($header->field === $field) {
                return $header->value;
            }

            if (mb_strtolower($header->field) === mb_strtolower($field)) {
                $fallbackValue = $header->value;
            }
        }

        return $fallbackValue;
    }
}
