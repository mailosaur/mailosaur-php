<?php

namespace Mailosaur\Models;


class EmailClientListResult
{
    /**
     * @var EmailClient[] A list of available email clients with which to generate email previews.
     */
    public $items = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'items') && is_array($data->items)) {
            foreach ($data->items as $item) {
                $this->items[] = new EmailClient($item);
            }
        }
    }
}
