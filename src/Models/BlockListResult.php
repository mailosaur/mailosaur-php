<?php

namespace Mailosaur\Models;


class BlockListResult
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var \Mailosaur\Models\ResultEnum */
    public $result;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'name')) {
            $this->name = $data->name;
        }

        if (property_exists($data, 'result')) {
            $this->result = ResultEnum::from($data->result);
        }
    }
}