<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Operations\Servers;
use Mailosaur\Models\SearchCriteria;
use Mailosaur\Models\PreviewRequest;
use Mailosaur\Models\PreviewRequestOptions;

class PreviewsTests extends \PHPUnit\Framework\TestCase
{
    /** @var \Mailosaur\MailosaurClient */
    protected static $client;

    /** @var string */
    protected static $server;

    public static function setUpBeforeClass(): void
    {
        $baseUrl      = ($h = getenv('MAILOSAUR_BASE_URL')) ? $h : 'https://mailosaur.com/';
        $apiKey       = getenv('MAILOSAUR_API_KEY');
        self::$server = getenv('MAILOSAUR_SERVER');

        if (empty($apiKey)) {
            throw new \Exception('Missing necessary environment variables - refer to README.md');
        }

        self::$client = new MailosaurClient($apiKey, $baseUrl);
    }

    public function testListEmailClients()
    {
        $result = self::$client->previews->allEmailClients();

        $this->assertTrue(count($result->items) > 1);
    }

    public function testGeneratePreviews()
    {
        if (empty(self::$server)) { $this->markTestSkipped(); }

        $randomString = Servers::randomString(10);
        $host             = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.net';
        $testEmailAddress = $randomString . '@' . self::$server . '.' . $host;

        Mailer::sendEmail(self::$client, self::$server, $testEmailAddress);

        $criteria         = new SearchCriteria();
        $criteria->sentTo = $testEmailAddress;

        $email = self::$client->messages->get(self::$server, $criteria);

        $request = new PreviewRequest('OL2021');
        $options = new PreviewRequestOptions(array($request));

        $result = self::$client->messages->generatePreviews($email->id, $options);
        $this->assertTrue(count($result->items) > 0);

        $file = self::$client->files->getPreview($result->items[0]->id);
        $this->assertNotNull($file);
    }

}
