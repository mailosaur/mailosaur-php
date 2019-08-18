<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\SearchCriteria;

class FilesTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient */
    public $client;

    /** @var string */
    public $server;

    /** @var \Mailosaur\Models\Message */
    public $email;

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

        $host             = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.io';
        $testEmailAddress = 'wait_for_test.' . $this->server . '@' . $host;

        Mailer::sendEmail($this->client, $this->server, $testEmailAddress);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = $testEmailAddress;

        $this->email = $this->client->messages->waitFor($this->server, $criteria);
    }

    public function testGetEmail()
    {
        $result = $this->client->files->getEmail($this->email->id);

        $this->assertNotNull($result);
        $this->assertTrue(strlen($result) > 0);
        $this->assertStringContainsString($this->email->subject, $result);
    }

    public function testGetAttachment()
    {
        $attachment = $this->email->attachments[0];

        $result = $this->client->files->getAttachment($attachment->id);

        $this->assertNotNull($result);
        $this->assertEquals($attachment->length, strlen($result));
    }

}