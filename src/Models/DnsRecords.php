<?php

namespace Mailosaur\Models;


class DnsRecords
{
    /** @var string[] */
    public $a;

    /** @var string[] */
    public $mx;

    /** @var string[] */
    public $ptr;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'a') && is_array($data->a)) {
            $this->a = $data->a;
        }

        if (property_exists($data, 'mx') && is_array($data->mx)) {
            $this->mx = $data->mx;
        }

        if (property_exists($data, 'ptr') && is_array($data->ptr)) {
            $this->ptr = $data->ptr;
        }
    }
}