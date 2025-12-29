<?php

namespace Mailosaur\Models;


class EmailClient
{
    /** @var string The unique email client label. Used when generating email preview requests. */
    public $label;

    /** @var string The display name of the email client. */
    public $name;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'label')) {
            $this->label = $data->label;
        }

        if (property_exists($data, 'name')) {
            $this->name = $data->name;
        }
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $model = array(
            'label' => $this->label,
            'name'  => $this->name
        );

        return $model;
    }

    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}
