<?php

namespace Mailosaur\Models;


class Metadata
{
    /** @var \Mailosaur\Models\MessageHeader[] Email headers. */
    public $headers = array();

    /**
     * @var string The fully-qualified domain name or IP address that was provided with the
     * Extended HELLO (EHLO) or HELLO (HELO) command. This value is generally
     * used to identify the SMTP client.
     * https://datatracker.ietf.org/doc/html/rfc5321#section-4.1.1.1
     */
    public $mailFrom;

    /**
     * @var \Mailosaur\Models\MessageAddress[] The source mailbox/email address, referred to as the 'reverse-path',
     * provided via the MAIL command during the SMTP transaction.
     * https://datatracker.ietf.org/doc/html/rfc5321#section-4.1.1.2
     */
    public $rcptTo = array();

    /**
     * @var string The recipient email addresses, each referred to as a 'forward-path',
     * provided via the RCPT command during the SMTP transaction.
     * https://datatracker.ietf.org/doc/html/rfc5321#section-4.1.1.3
     */
    public $ehlo;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'headers') && is_array($data->headers)) {
            foreach ($data->headers as $header) {
                $this->headers[] = new MessageHeader($header);
            }
        }

        if (property_exists($data, 'mailFrom')) {
            $this->mailFrom = $data->mailFrom;
        }

        if (property_exists($data, 'rcptTo') && is_array($data->rcptTo)) {
            foreach ($data->rcptTo as $rcptTo) {
                $this->rcptTo[] = new MessageAddress($rcptTo);
            }
        }

        if (property_exists($data, 'ehlo')) {
            $this->ehlo = $data->ehlo;
        }
    }
}