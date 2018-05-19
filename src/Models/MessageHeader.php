<?php

namespace Mailosaur\Models;


class MessageHeader
{
    /** @var string Header key. */
    public $field;

    /** @var string Header value. */
    public $value;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'field')) {
            $this->field = $data->field;
        }

        if (property_exists($data, 'value')) {
            $this->value = $data->value;
        }
    }
}