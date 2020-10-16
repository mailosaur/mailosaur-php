<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\Message;
use Mailosaur\Models\MessageSummary;
use Mailosaur\Models\SearchCriteria;

class EmailsTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient
     */
    public $client;

    /** @var string */
    public $server;

    /** @var \Mailosaur\Models\MessageSummary[] */
    public $emails;


    public function setUp(): void
    {
        $baseUrl      = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey       = getenv('MAILOSAUR_API_KEY');
        $this->server = getenv('MAILOSAUR_SERVER');

        if (empty($apiKey) || empty($this->server)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        $this->client = new MailosaurClient($apiKey, $baseUrl);

        $this->client->messages->deleteAll($this->server);

        Mailer::sendEmails($this->client, $this->server, 5);

        $this->emails = $this->client->messages->all($this->server)->items;
    }

    public function testListWithReceivedAfter()
    {
        $pastDate = new \DateTime();
        $pastDate->sub(new \DateInterval('PT10M'));
        $pastEmails = $this->client->messages->all($this->server, null, null, $pastDate)->items;
        $this->assertTrue(count($pastEmails) > 0);

        $futureEmails = $this->client->messages->all($this->server, null, null, new \DateTime())->items;
        $this->assertCount(0, $futureEmails);
    }

    public function testGet()
    {
        $host             = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.io';
        $testEmailAddress = 'wait_for_test.' . $this->server . '@' . $host;

        Mailer::sendEmail($this->client, $this->server, $testEmailAddress);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = $testEmailAddress;

        $email = $this->client->messages->get($this->server, $criteria);

        $this->validateEmail($email);
    }

    public function testGetById()
    {
        $emailToRetrieve = $this->emails[0];

        $email = $this->client->messages->getById($emailToRetrieve->id);

        $this->validateEmail($email);
        $this->validateHeaders($email);
    }

    public function testGetByIdNotFound()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        $this->client->messages->getById(uniqid());
    }

    public function testSearchNoCriteriaError()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        $this->client->messages->search($this->server, new SearchCriteria());
    }

    public function testSearchBySentTo()
    {
        $targetEmail = $this->emails[1];

        $criteria = new SearchCriteria();

        $criteria->sentTo = $targetEmail->to[0]->email;

        $results = $this->client->messages->search($this->server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchBySentToInvalidEmail()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = '.not_an_email_address';

        $this->client->messages->search($this->server, $criteria);
    }

    public function testSearchByBody()
    {
        $targetEmail    = $this->emails[1];
        $uniqueString   = substr($targetEmail->subject, 0, 10);
        $criteria       = new SearchCriteria();
        $criteria->body = $uniqueString . ' html';

        $results = $this->client->messages->search($this->server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchBySubject()
    {
        $targetEmail       = $this->emails[1];
        $criteria          = new SearchCriteria();
        $criteria->subject = substr($targetEmail->subject, 0, 10);

        $results = $this->client->messages->search($this->server, $criteria)->items;

        $this->assertCount(1, $results);
        $this->assertEquals($targetEmail->to[0]->email, $results[0]->to[0]->email);
        $this->assertEquals($targetEmail->subject, $results[0]->subject);
    }

    public function testSearchWithSpecialCharacters()
    {
        $criteria          = new SearchCriteria();
        $criteria->subject = 'Search with ellipsis … and emoji 👨🏿‍🚒';

        $results = $this->client->messages->search($this->server, $criteria)->items;

        $this->assertCount(0, $results);
    }

    public function testSpamAnalysis()
    {
        $targetId = $this->emails[0]->id;

        $result = $this->client->analysis->spam($targetId);

        foreach ($result->spamFilterResults->spamAssassin as $rule) {
            $this->assertNotEmpty($rule->rule);
            $this->assertNotEmpty($rule->description);
        }
    }

    public function testDelete()
    {
        $targetEmailId = $this->emails[4]->id;

        $this->client->messages->delete($targetEmailId);

        $this->expectException(\Mailosaur\Models\MailosaurException::class);

        $this->client->messages->delete($targetEmailId);
    }

    public function testAll()
    {
        foreach ($this->emails as $emailSummary) {
            $this->validateEmailSummary($emailSummary);
        }
    }

    private function validateEmailSummary(MessageSummary $email)
    {
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