<?php

namespace Mailosaur\Models;


class PreviewListResult
{
    /**
     * @var Preview[] A list of requested email previews.
     */
    public $items = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'items') && is_array($data->items)) {
            foreach ($data->items as $item) {
                $this->items[] = new Preview($item);
            }
        }
    }
}
