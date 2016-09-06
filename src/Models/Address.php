<?php namespace Mailosaur\Models;


class Address
{
    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $name;

    public function __construct(\stdClass $address = null)
    {
        if ($address !== null) {

            if (property_exists($address, 'address')) {
                $this->address = $address->address;
            }

            if (property_exists($address, 'name')) {
                $this->name = $address->name;
            }
        }
    }

    /**
     * Get full email address
     *
     * @return string
     */
    public function getFullAddress()
    {
        return $this->name . ' <' . $this->address . '>';
    }
}