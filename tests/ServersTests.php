<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\ServerCreateOptions;

class ServersTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient */
    public $mClient;

    public function setUp(): void
    {
        $baseUrl = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey  = getenv('MAILOSAUR_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        $this->mClient = new MailosaurClient($apiKey, $baseUrl);
    }

    public function testAll()
    {
        $servers = $this->mClient->servers->all()->items;

        $this->assertTrue(count($servers) > 1);
    }

    public function testGetNotFound()
    {
        $this->expectException(\Mailosaur\Models\MailosaurException::class);

        $this->mClient->servers->get("efe907e9-74ed-4113-a3e0-a3d41d914765");
    }

    public function testCrud()
    {
        $serverName = "My test";

        // Create a new server
        $options       = new ServerCreateOptions($serverName);
        $createdServer = $this->mClient->servers->create($options);

        $this->assertFalse(empty($createdServer->id));
        $this->assertEquals($serverName, $createdServer->name);
        $this->assertNotNull($createdServer->password);
        $this->assertNotNull($createdServer->users);
        $this->assertEquals(0, $createdServer->messages);
        $this->assertNotNull($createdServer->forwardingRules);

        // Retrieve a server and confirm it has expected content
        $retrievedServer = $this->mClient->servers->get($createdServer->id);

        $this->assertEquals($createdServer->id, $retrievedServer->id);
        $this->assertEquals($createdServer->name, $retrievedServer->name);
        $this->assertNotNull($retrievedServer->password);
        $this->assertNotNull($retrievedServer->users);
        $this->assertEquals(0, $retrievedServer->messages);
        $this->assertNotNull($retrievedServer->forwardingRules);

        // Update a server and confirm it has changed
        $retrievedServer->name = $retrievedServer->name . ' EDITED';

        $updatedServer = $this->mClient->servers->update($retrievedServer->id, $retrievedServer);
        $this->assertEquals($retrievedServer->id, $updatedServer->id);
        $this->assertEquals($retrievedServer->name, $updatedServer->name);
        $this->assertEquals($retrievedServer->password, $updatedServer->password);
        $this->assertEquals($retrievedServer->users, $updatedServer->users);
        $this->assertEquals($retrievedServer->messages, $updatedServer->messages);
        $this->assertEquals($retrievedServer->forwardingRules, $updatedServer->forwardingRules);

        $this->mClient->servers->delete($retrievedServer->id);

        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        $this->mClient->servers->delete($retrievedServer->id);
    }

    public function testFailedCreate()
    {
        $options = new ServerCreateOptions();

        $this->expectException(\Mailosaur\Models\MailosaurException::class);
        $this->expectExceptionMessage("Operation returned an invalid status code 'Bad Request'");

        $this->mClient->servers->create($options);
    }
}