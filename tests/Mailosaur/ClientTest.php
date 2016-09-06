<?php namespace Test\Mailosaur;

use Mailosaur\Client;
use Mailosaur\Models\Address;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $mailbox = false;
    protected $password = "";
    protected $apiUrl = false;
    protected $apiKey = false;
    protected $smtpHost = 'mailosaur.io';
    protected $smtpPort = 25;
    protected $useGenerateEmail = false;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var SmtpMailer
     */
    protected $mailClient;

    public function setUp()
    {
        $this->mailbox = getenv('MAILOSAUR_MAILBOX_ID');
        $this->apiUrl  = getenv('MAILOSAUR_BASE_URL');
        $this->apiKey  = getenv('MAILOSAUR_API_KEY');

        !getenv('MAILOSAUR_MAILBOX_PASSWORD') || $this->password = getenv('MAILOSAUR_MAILBOX_PASSWORD');
        !getenv('MAILOSAUR_SMTP_HOST') || $this->smtpHost = getenv('MAILOSAUR_SMTP_HOST');
        !getenv('MAILOSAUR_SMTP_PORT') || $this->smtpPort = getenv('MAILOSAUR_SMTP_PORT');
        !getenv('MAILOSAUR_USE_GENERATE') || $this->useGenerateEmail = true;

        $this->client     = new Client($this->apiKey, $this->mailbox, $this->apiUrl);
        $this->mailClient = new SmtpMailer(array(
            'host'     => $this->smtpHost,
            'port'     => $this->smtpPort,
            'username' => $this->mailbox,
            'password' => $this->password
        ));
    }

    public function testGenerateEmails()
    {
        $emails = array();
        for ($i = 0; $i < 1000; $i++) {
            $address = $this->client->generateEmailAddress();
            if (!in_array($address, $emails)) {
                $emails[] = $address;
            }
        }

        self::assertCount(1000, $emails);
    }

    public function testInitial()
    {
        $this->client->emptyMailBox();

        self::assertNotFalse($this->mailbox, 'mailbox is not set.');
        self::assertNotFalse($this->apiKey, 'api key is not set.');
        self::assertNotFalse($this->smtpHost, 'smtp host is not set.');
        self::assertNotFalse($this->password, 'smtp connection password is not set.');
        self::assertCount(0, $this->client->getEmails());
    }

    /**
     * @depends testInitial
     */
    public function testGetEmails()
    {
        $this->sendTestEmail();
        $second = $this->sendTestEmail();
        $this->sendTestEmail();

        $emails = $this->client->getEmails();

        self::assertCount(3, $emails);
        self::assertEmail($second, $emails[1]);
    }

    /**
     * @depends testInitial
     */
    public function testGetEmailsBySearchPattern()
    {
        $this->sendTestEmail();
        $second = $this->sendTestEmail();
        $this->sendTestEmail();

        $emails = $this->client->getEmailsBySearchPattern($second['subject']);

        self::assertCount(1, $emails);
        self::assertEmail($second, $emails[0]);
    }


    /**
     * @depends testInitial
     */
    public function testGetEmailsByRecipient()
    {
        $this->sendTestEmail();
        $second = $this->sendTestEmail();
        $this->sendTestEmail();

        $emails = $this->client->getEmailsByRecipient($second['to']->address);

        self::assertCount(1, $emails);
        self::assertEmail($second, $emails[0]);
    }


    /**
     * @depends testInitial
     */
    public function testGetEmail()
    {
        $emailInfo = $this->sendTestEmail();

        $emails = $this->client->getEmails();

        self::assertCount(1, $emails);

        $email = $this->client->getEmail($emails[0]->id);

        $this->assertEmail($emailInfo, $email);
    }

    public static function assertEmail($emailInfo, $email)
    {
        self::assertInstanceOf('\Mailosaur\Models\Email', $email);
        self::assertInstanceOf('\Mailosaur\Models\Address', $email->to[0]);
        self::assertInstanceOf('\Mailosaur\Models\Address', $email->from[0]);
        self::assertInstanceOf('\Mailosaur\Models\Attachment', $email->attachments[0]);
        self::assertInstanceOf('\DateTime', $email->creationDate);
        self::assertInstanceOf('\stdClass', $email->headers);

        self::assertCount(1, $email->to);
        self::assertEquals($emailInfo['to'], $email->to[0]);

        self::assertCount(1, $email->from);
        self::assertEquals($emailInfo['from'], $email->from[0]);

        self::assertEquals($emailInfo['subject'], $email->subject);
        self::assertEquals('normal', $email->priority);

        self::assertEquals($emailInfo['html'], $email->html);
        self::assertEquals($emailInfo['text'], $email->text);

        self::assertEquals(count($emailInfo['textLinks']), count($email->textLinks));

        foreach ($email->textLinks as $key => $link) {
            self::assertArrayHasKey($key, $emailInfo['textLinks']);
            self::assertEquals($emailInfo['textLinks'][$key], $link->href);
        }

        self::assertEquals(count($emailInfo['htmlLinks']), count($email->htmlLinks));

        foreach ($email->htmlLinks as $key => $link) {
            self::assertArrayHasKey($key, $emailInfo['htmlLinks']);
            self::assertEquals($emailInfo['htmlLinks'][$key]['href'], $link->href);
            self::assertEquals($emailInfo['htmlLinks'][$key]['text'], $link->text);
        }

        self::assertEquals(count($emailInfo['images']), count($email->images));
        self::assertEquals(count($emailInfo['images']) + count($emailInfo['attachments']), count($email->attachments));
        self::assertEquals(count($emailInfo['attachments']), count($email->attachments) - count($email->images));

        foreach ($email->images as $image) {
            self::assertArrayHasKey($image->alt, $emailInfo['images']);
            self::assertStringStartsWith('cid:', $image->src);
        }

        foreach ($email->attachments as $attachment) {
            if (!empty($attachment->contentId)) {
                self::assertArrayHasKey($attachment->fileName, $emailInfo['attachments']);
                self::assertEquals(filesize($emailInfo['attachments'][$attachment->fileName]), $attachment->length);
                self::assertEquals(mime_content_type($emailInfo['attachments'][$attachment->fileName]), $attachment->contentType);
            }
        }

    }

    public function testEmptyMailbox()
    {
        $this->sendTestEmail();
        $this->sendTestEmail();

        $this->client->emptyMailBox();

        self::assertCount(0, $this->client->getEmails());
    }

    /**
     * @depends testInitial
     */
    public function testDeleteEmail()
    {
        $first = $this->sendTestEmail();
        $this->sendTestEmail();

        $emailsFiltered = $this->client->getEmailsByRecipient($first['to']->address);

        self::assertEquals(2, count($this->client->getEmails()));
        self::assertEquals(1, count($emailsFiltered));
        self::assertInstanceOf('\Mailosaur\Models\Email', $this->client->getEmail($emailsFiltered[0]->id));

        $this->client->deleteEmail($emailsFiltered[0]->id);

        self::assertEquals(0, count($this->client->getEmailsByRecipient($first['to']->address)));
    }

    /**
     * @depends testInitial
     */
    public function testGetEML()
    {
        $first = $this->sendTestEmail();
        $this->sendTestEmail();

        $emailsFiltered = $this->client->getEmailsByRecipient($first['to']->address);

        self::assertEquals(2, count($this->client->getEmails()));
        self::assertEquals(1, count($emailsFiltered));
        self::assertInstanceOf('\Mailosaur\Models\Email', $this->client->getEmail($emailsFiltered[0]->id));

        self::assertNotEmpty($this->client->getEML($emailsFiltered[0]->rawId));
    }

    /**
     * @depends testInitial
     */
    public function testGetAttachment()
    {
        $first = $this->sendTestEmail();
        $this->sendTestEmail();

        $emailsFiltered = $this->client->getEmailsByRecipient($first['to']->address);

        self::assertEquals(2, count($this->client->getEmails()));
        self::assertEquals(1, count($emailsFiltered));
        self::assertInstanceOf('\Mailosaur\Models\Email', $this->client->getEmail($emailsFiltered[0]->id));

        $attachment = $this->client->getAttachment($emailsFiltered[0]->attachments[0]->id);

        self::assertNotEmpty($attachment);
        self::assertEquals($emailsFiltered[0]->attachments[0]->length, strlen($attachment));
    }

    public function sendTestEmail()
    {
        $emailInfo = $this->createTestData();

        $message = new Message();
        $message
            ->setFrom($emailInfo['from']->getFullAddress())
            ->addTo($emailInfo['to']->getFullAddress())
            ->setSubject($emailInfo['subject'])
            ->setHtmlBody($emailInfo['html'], __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)
            ->setBody($emailInfo['text']);

        foreach ($emailInfo['attachments'] as $attachment) {
            $message->addAttachment($attachment);
        }

        $emailInfo['html'] = $message->getHtmlBody();

        $this->mailClient->send($message);

        return $emailInfo;
    }

    public function createTestData()
    {
        $faker = \Faker\Factory::create();

        $htmlLinks = array();
        $textLinks = array();

        for ($i = 0; $i < 3; $i++) {
            $htmlLinks[] = array(
                'href' => $faker->url,
                'text' => $faker->word,
            );
        }

        for ($i = 0; $i < 2; $i++) {
            $textLinks[] = $faker->url;
        }

        $emailInfo = array(
            'from'        => new Address((object)array('address' => $faker->email, 'name' => $faker->name)),
            'to'          => new Address((object)array('address' => $this->useGenerateEmail ? $this->client->generateEmailAddress() : $faker->email, 'name' => $faker->name)),
            'subject'     => $faker->text(50),
            'html'        => '',
            'text'        => $faker->text(200),
            'htmlLinks'   => $htmlLinks,
            'textLinks'   => $textLinks,
            'images'      => array(
                $faker->md5 => 'logo-m-circle-sm.png',
                $faker->md5 => 'logo-m.png'
            ),
            'attachments' => array(
                'logo-m-circle-sm.png' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logo-m-circle-sm.png',
                'logo-m.png'           => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logo-m.png'
            ),
        );

        array_walk($htmlLinks, function (&$v, $k) { $v = '<a href="' . $v['href'] . '">' . $v['text'] . '</a>'; });


        $emailInfo['html'] .= $faker->text(200);
        $emailInfo['html'] .= '<b> html links: </b>';

        $emailInfo['html'] .= implode(' ', $htmlLinks);

        $emailInfo['text'] .= ' text links: ';
        $emailInfo['text'] .= implode(' ', $textLinks);

        foreach ($emailInfo['images'] as $alt => $src) {
            $emailInfo['html'] .= '<img src="' . $src . '" alt="' . $alt . '" />';
        }

        $this->emailTestData = $emailInfo;

        return $emailInfo;
    }

    public function tearDown()
    {
        $this->client->emptyMailBox();
    }
}