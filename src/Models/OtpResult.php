<?php

namespace Mailosaur\Models;


class OtpResult
{
    /** @var string The current one-time password. */
    public $code;

    /** @var \DateTime The expiry date/time of the current one-time password. */
    public $expires;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'code')) {
            $this->code = $data->code;
        }

        if (property_exists($data, 'expires')) {
            $this->expires = new \DateTime($data->expires);
        }
    }
}
