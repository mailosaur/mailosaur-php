<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\ServerCreateOptions;
use Mailosaur\Models\MailosaurException;

class ErrorsTests extends \PHPUnit\Framework\TestCase
{
    public function testUnauthorized()
    {
        try {
            $client = new MailosaurClient('invalid_key');
            $client->servers->all();
        } catch(MailosaurException $e) {
            $this->assertEquals('Authentication failed, check your API key.', $e->getMessage());
        }
    }

    public function testNotFound()
    {
        try {
            $client = new MailosaurClient(getenv('MAILOSAUR_API_KEY'));
            $client->servers->get('not_found');
        } catch(MailosaurException $e) {
            $this->assertEquals('Not found, check input parameters.', $e->getMessage());
        }
    }

    public function testBadRequest()
    {
        try {
            $client = new MailosaurClient(getenv('MAILOSAUR_API_KEY'));
            $options = new ServerCreateOptions();
            $client->servers->create($options);
        } catch(MailosaurException $e) {
            $this->assertEquals('(name) Servers need a name\r\n', $e->getMessage());
        }
    }
}
