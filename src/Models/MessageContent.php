<?php

namespace Mailosaur\Models;


class MessageContent
{
    /** @var Link[] */
    public $links = array();

    /** @var Image[] */
    public $images = array();

    /** @var string */
    public $body;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'links') && is_array($data->links)) {
            foreach ($data->links as $link) {
                $this->links[] = new Link($link);
            }
        }

        if (property_exists($data, 'images') && is_array($data->images)) {
            foreach ($data->images as $image) {
                $this->images[] = new Image($image);
            }
        }

        if (property_exists($data, 'body')) {
            $this->body = $data->body;
        }
    }
}