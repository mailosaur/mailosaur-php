<?php

namespace Mailosaur\Models;


class Metadata
{
    /** @var \Mailosaur\Models\MessageHeader[] Email headers. */
    public $headers = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'headers') && is_array($data->headers)) {
            foreach ($data->headers as $header) {
                $this->headers[] = new MessageHeader($header);
            }
        }
    }
}