<?php

namespace Mailosaur\Models;


class ServerListResult
{
    /**
     * @var Server[] Servers are returned sorted by creation date, with the most recently-created server appearing first.
     */
    public $items = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'items') && is_array($data->items)) {
            foreach ($data->items as $item) {
                $this->items[] = new Server($item);
            }
        }
    }
}