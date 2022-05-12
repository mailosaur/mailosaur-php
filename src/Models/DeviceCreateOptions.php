<?php

namespace Mailosaur\Models;


class DeviceCreateOptions
{
    /** @var string A name used to identify the device. */
    public $name;

    /** @var string The base32-encoded shared secret for this device. */
    public $sharedSecret;

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $options = array(
            'name'            => $this->name,
            'sharedSecret'    => $this->sharedSecret
        );

        return $options;
    }

    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}
