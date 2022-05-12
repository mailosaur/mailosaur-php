<?php

namespace Mailosaur\Models;


class Device
{
    /** @var string Unique identifier for the device. */
    public $id;

    /** @var string A name used to identify the device. */
    public $name;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'name')) {
            $this->name = $data->name;
        }
    }
}
