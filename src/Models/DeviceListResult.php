<?php

namespace Mailosaur\Models;


class DeviceListResult
{
    /**
     * @var Device[] The individual devices forming the result.
     */
    public $items = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'items') && is_array($data->items)) {
            foreach ($data->items as $item) {
                $this->items[] = new Device($item);
            }
        }
    }
}
