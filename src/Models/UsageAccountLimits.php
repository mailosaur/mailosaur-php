<?php

namespace Mailosaur\Models;


class UsageAccountLimits
{
    /** @var \Mailosaur\Models\UsageAccountLimit */
    public $servers;

    /** @var \Mailosaur\Models\UsageAccountLimit */
    public $users;

    /** @var \Mailosaur\Models\UsageAccountLimit */
    public $email;

    /** @var \Mailosaur\Models\UsageAccountLimit */
    public $sms;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'servers')) {
            $this->servers = new UsageAccountLimit($data->servers);
        }

        if (property_exists($data, 'users')) {
            $this->users = new UsageAccountLimit($data->users);
        }

        if (property_exists($data, 'email')) {
            $this->email = new UsageAccountLimit($data->email);
        }

        if (property_exists($data, 'sms')) {
            $this->sms = new UsageAccountLimit($data->sms);
        }
    }
}
