<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\ServerCreateOptions;
use Mailosaur\Models\MailosaurException;

class ErrorsTests extends \PHPUnit\Framework\TestCase
{
    protected static $apiKey;
    protected static $baseUrl;

    public static function setUpBeforeClass(): void
    {
        self::$baseUrl = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        self::$apiKey  = getenv('MAILOSAUR_API_KEY');

        if (empty(self::$apiKey)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }
    }

    public function testUnauthorized()
    {
        try {
            $client = new MailosaurClient('invalid_key', self::$baseUrl);
            $client->servers->all();
        } catch(MailosaurException $e) {
            $this->assertEquals('Authentication failed, check your API key.', $e->getMessage());
        }
    }

    public function testNotFound()
    {
        try {
            $client = new MailosaurClient(self::$apiKey, self::$baseUrl);
            $client->servers->get('not_found');
        } catch(MailosaurException $e) {
            $this->assertEquals('Not found, check input parameters.', $e->getMessage());
        }
    }

    public function testBadRequest()
    {
        try {
            $client = new MailosaurClient(self::$apiKey, self::$baseUrl);
            $options = new ServerCreateOptions();
            $client->servers->create($options);
        } catch(MailosaurException $e) {
            $this->assertEquals('(name) Servers need a name\r\n', $e->getMessage());
        }
    }
}
