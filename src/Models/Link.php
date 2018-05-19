<?php

namespace Mailosaur\Models;


class Link
{
    /** @var string */
    public $href;

    /** @var string */
    public $text;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'href')) {
            $this->href = $data->href;
        }

        if (property_exists($data, 'text')) {
            $this->text = $data->text;
        }
    }
}