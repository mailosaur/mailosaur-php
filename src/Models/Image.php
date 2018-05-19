<?php

namespace Mailosaur\Models;


class Image
{
    /** @var string */
    public $src;

    /** @var string */
    public $alt;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'src')) {
            $this->src = $data->src;
        }

        if (property_exists($data, 'alt')) {
            $this->alt = $data->alt;
        }
    }
}