<?php

namespace Mailosaur\Models;


class Code
{
    /** @var string */
    public $value;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'value')) {
            $this->value = $data->value;
        }
    }
}
