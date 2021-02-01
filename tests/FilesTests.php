<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\SearchCriteria;

class FilesTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient */
    protected static $client;

    /** @var string */
    protected static $server;

    /** @var \Mailosaur\Models\Message */
    protected static $email;

    public static function setUpBeforeClass(): void
    {
        $baseUrl      = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey       = getenv('MAILOSAUR_API_KEY');
        self::$server = getenv('MAILOSAUR_SERVER');

        if (empty($apiKey) || empty(self::$server)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        self::$client = new MailosaurClient($apiKey, $baseUrl);

        self::$client->messages->deleteAll(self::$server);

        $host             = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.net';
        $testEmailAddress = 'wait_for_test@' . self::$server . '.' . $host;

        Mailer::sendEmail(self::$client, self::$server, $testEmailAddress);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = $testEmailAddress;

        self::$email = self::$client->messages->get(self::$server, $criteria);
    }

    public function testGetEmail()
    {
        $result = self::$client->files->getEmail(self::$email->id);

        $this->assertNotNull($result);
        $this->assertTrue(strlen($result) > 0);
        $this->assertStringContainsString(self::$email->subject, $result);
    }

    public function testGetAttachment()
    {
        $attachment = self::$email->attachments[0];

        $result = self::$client->files->getAttachment($attachment->id);

        $this->assertNotNull($result);
        $this->assertEquals($attachment->length, strlen($result));
    }

}