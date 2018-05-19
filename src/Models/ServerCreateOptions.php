<?php

namespace Mailosaur\Models;


class ServerCreateOptions
{
    /** @var string A name used to identify the server. */
    public $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function toJsonString()
    {
        return json_encode(array('name' => $this->name));
    }
}