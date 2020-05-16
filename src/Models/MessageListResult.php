<?php

namespace Mailosaur\Models;


class MessageListResult
{
    /** @var array|MessageSummary[] */
    public $items = array();

    public function __construct(\stdClass $data = null)
    {
        if ($data !== null && property_exists($data, 'items') && is_array($data->items)) {
            foreach ($data->items as $item) {
                $this->items[] = new MessageSummary($item);
            }
        }
    }
}