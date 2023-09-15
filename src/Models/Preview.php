<?php

namespace Mailosaur\Models;


class Preview
{
    /** @var string The unique identifier for the email preview. */
    public $id;

    /** @var string The email client the preview was generated with. */
    public $emailClient;

    /** @var bool Whether images were disabled in the preview. */
    public $disableImages;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'emailClient')) {
            $this->emailClient = $data->emailClient;
        }

        if (property_exists($data, 'disableImages')) {
            $this->disableImages = $data->disableImages;
        }
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $model = array(
            'id'              => $this->id,
            'emailClient'     => $this->emailClient,
            'disableImages'   => $this->disableImages
        );

        return $model;
    }

    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}
