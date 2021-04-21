<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\ServerCreateOptions;
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

    public function testAll()
    {
        $servers = self::$client->servers->all()->items;

        $this->assertTrue(count($servers) > 1);
    }

    public function testGetNotFound()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);

        self::$client->servers->get("efe907e9-74ed-4113-a3e0-a3d41d914765");
    }

    public function testCrud()
    {
        $serverName = "My test";

        // Create a new server
        $options       = new ServerCreateOptions($serverName);
        $createdServer = self::$client->servers->create($options);

        $this->assertFalse(empty($createdServer->id));
        $this->assertEquals($serverName, $createdServer->name);
        $this->assertNotNull($createdServer->users);
        $this->assertEquals(0, $createdServer->messages);

        // Retrieve a server and confirm it has expected content
        $retrievedServer = self::$client->servers->get($createdServer->id);

        $this->assertEquals($createdServer->id, $retrievedServer->id);
        $this->assertEquals($createdServer->name, $retrievedServer->name);
        $this->assertNotNull($retrievedServer->users);
        $this->assertEquals(0, $retrievedServer->messages);

        // Retrieve server password
        $password = self::$client->servers->getPassword($createdServer->id);

        $this->assertTrue($password->length >= 8);

        // Update a server and confirm it has changed
        $retrievedServer->name = $retrievedServer->name . ' updated with ellipsis … and emoji 👨🏿‍🚒';

        $updatedServer = self::$client->servers->update($retrievedServer->id, $retrievedServer);
        $this->assertEquals($retrievedServer->id, $updatedServer->id);
        $this->assertEquals($retrievedServer->name, $updatedServer->name);
        $this->assertEquals($retrievedServer->users, $updatedServer->users);
        $this->assertEquals($retrievedServer->messages, $updatedServer->messages);

        self::$client->servers->delete($retrievedServer->id);

        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        self::$client->servers->delete($retrievedServer->id);
    }

    public function testFailedCreate()
    {
        try {
            $options = new ServerCreateOptions();
            self::$client->servers->create($options);
        } catch(MailosaurException $e) {
            $this->assertEquals('Request had one or more invalid parameters.', $e->getMessage());
            $this->assertEquals('invalid_request', $e->errorType);
            $this->assertEquals(400, $e->httpStatusCode);
            $this->assertEquals('{"type":"ValidationError","messages":{"name":"Please provide a name for your server"}}', $e->httpResponseBody);
        }
    }
}