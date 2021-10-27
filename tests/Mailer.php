<?php

namespace MailosaurTest;


use Mailosaur\MailosaurClient;
use Mailosaur\Operations\Servers;
use Nette\Mail\SmtpMailer;

class Mailer
{
    private static $sHtmlFile = 'Resources/testEmail.html';
    private static $sTextFile = 'Resources/testEmail.txt';

    public static function sendEmails(MailosaurClient $client, $server, $quantity)
    {
        for ($i = 0; $i < $quantity; $i++) {
            self::sendEmail($client, $server);
        }
    }

    public static function sendEmail(MailosaurClient $client, $server, $sendToAddress = null)
    {
        $mailer = new SmtpMailer(array(
            'host' => ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.net',
            'port' => ($p = getenv('MAILOSAUR_SMTP_PORT')) ? (int)$p : 25
        ));

        $randomString = Servers::randomString();
        $verifiedDomain = getenv('MAILOSAUR_VERIFIED_DOMAIN');

        $from          = join(' ', array($randomString, $randomString, '<' . $randomString . '@' . $verifiedDomain . '>'));
        $sendToAddress = join(' ', array($randomString, $randomString, '<' . (($sendToAddress === null) ? $client->servers->generateEmailAddress($server) : $sendToAddress) . '>'));
        $htmlString    = str_replace('REPLACED_DURING_TEST', $randomString, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$sHtmlFile));
        $textString    = str_replace('REPLACED_DURING_TEST', $randomString, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . self::$sTextFile));

        $message = new \Nette\Mail\Message();

        $message
            ->setFrom($from)
            ->addTo($sendToAddress)
            ->setSubject($randomString . ' subject');

        $message->setBody($textString);
        $message->setHtmlBody($htmlString, false);
        $message->setEncoding('UTF-8');

        $message->addEmbeddedFile('ii_1435fadb31d523f6', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/cat.png'))
                ->setHeader('Content-ID', 'ii_1435fadb31d523f6')
                ->setContentType('image/png');

        $message->addAttachment(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/dog.png')
                ->setContentType('image/png')
                ->setHeader('Content-Disposition', 'attachment; filename="Resources/dog.png"');

        $mailer->send($message);
    }
}
