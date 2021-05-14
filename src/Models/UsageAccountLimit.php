<?php

namespace Mailosaur\Models;


class UsageAccountLimit
{
    /**
     * @var int The limit.
     */
    public $limit;

    /**
     * @var int The current usage.
     */
    public $current;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'limit')) {
            $this->limit = $data->limit;
        }

        if (property_exists($data, 'current')) {
            $this->current = $data->current;
        }
    }
}
