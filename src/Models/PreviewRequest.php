<?php

namespace Mailosaur\Models;


class PreviewRequest
{
    /** @var string The email client you wish to generate a preview for. */
    public $emailClient;

    /** @var bool Whether images will be disabled (only if supported by the client). */
    public $disableImages;

    public function __construct($emailClient, $disableImages = false)
    {
        $this->emailClient = $emailClient;
        $this->disableImages = $disableImages;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $model = array(
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
