<?php

namespace Mailosaur\Models;


class MessageAddress
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    public function __construct($address = null)
    {
        if ($address !== null) {
            if (property_exists($address, 'name')) {
                $this->name = $address->name;
            }

            if (property_exists($address, 'email')) {
                $this->email = $address->email;
            }

            if (property_exists($address, 'phone')) {
                $this->phone = $address->phone;
            }
        }
    }
}