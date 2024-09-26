<?php

namespace Mailosaur\Models;


class EmailAuthenticationResult
{
    /** @var \Mailosaur\Models\ResultEnum */
    public $result;

    /** @var string */
    public $description;

    /** @var string */
    public $rawValue;

    /** @var [] */
    public $tags;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'result')) {
            $this->result = ResultEnum::from($data->result);
        }

        if (property_exists($data, 'description')) {
            $this->description = $data->description;
        }

        if (property_exists($data, 'rawValue')) {
            $this->rawValue = $data->rawValue;
        }

        if (property_exists($data, 'tags') && is_array($data->tags)) {
            foreach ($data->tags as $tag) {
                if (is_object($tag)) {
                    foreach ($tag as $key => $value) {
                        $this->tags[$key] = $value;
                    }
                }
            }
        }
    }
}