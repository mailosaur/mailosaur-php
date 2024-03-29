<?php

namespace Mailosaur_Test;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\DeviceCreateOptions;
use Mailosaur\Models\MailosaurException;

class DevicesTests extends \PHPUnit\Framework\TestCase
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

    public function testCrud()
    {
        $deviceName = "My test";
        $sharedSecret = "ONSWG4TFOQYTEMY=";

        // Create a new device
        $options       = new DeviceCreateOptions();
        $options->name = $deviceName;
        $options->sharedSecret = $sharedSecret;
        $createdDevice = self::$client->devices->create($options);

        $this->assertFalse(empty($createdDevice->id));
        $this->assertEquals($deviceName, $createdDevice->name);

        // Retrieve an otp via device ID
        $otpResult = self::$client->devices->otp($createdDevice->id);
        $this->assertEquals(6, strlen($otpResult->code));

        $before = self::$client->devices->all();
        $this->assertTrue(in_array($createdDevice->id, array_column($before->items, 'id')));

        self::$client->devices->delete($createdDevice->id);

        $after = self::$client->devices->all();
        $this->assertFalse(in_array($createdDevice->id, array_column($after->items, 'id')));
    }

    public function testOtpViaSharedSecret()
    {
        $sharedSecret = "ONSWG4TFOQYTEMY=";

        $otpResult = self::$client->devices->otp($sharedSecret);
        $this->assertEquals(6, strlen($otpResult->code));
    }
}
