<?php

namespace Mailosaur\Models;


class UsageTransaction
{
    /**
     * @var \DateTime The datetime that this transaction occurred.
     */
    public $timestamp;

    /**
     * @var int The email count.
     */
    public $email;

    /**
     * @var int The SMS count.
     */
    public $sms;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'timestamp')) {
            $this->timestamp = new \DateTime($data->timestamp);
        }

        if (property_exists($data, 'email')) {
            $this->email = $data->email;
        }

        if (property_exists($data, 'sms')) {
            $this->sms = $data->sms;
        }
    }
}
