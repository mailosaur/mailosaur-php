<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\MailosaurException;

class ServersTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient */
    protected static $client;

    public static function setUpBeforeClass(): void
    {
        $baseUrl = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey  = getenv('MAILOSAUR_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        self::$client = new MailosaurClient($apiKey, $baseUrl);
    }

    public function testLimits()
    {
        $result = self::$client->usage->limits();

        $this->assertNotNull($result->servers);
        $this->assertNotNull($result->users);
        $this->assertNotNull($result->email);
        $this->assertNotNull($result->sms);

        $this->assertTrue($result->servers->limit > 1);
        $this->assertTrue($result->users->limit > 1);
        $this->assertTrue($result->email->limit > 1);
        $this->assertTrue($result->sms->limit > 1);
    }

    public function testTransactions()
    {
        $result = self::$client->usage->transactions();

        $this->assertTrue(count($result->items) > 1);
        $this->assertNotNull($result->items[0]->timestamp);
        $this->assertNotNull($result->items[0]->email);
        $this->assertNotNull($result->items[0]->sms);
    }
}