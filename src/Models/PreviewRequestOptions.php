<?php

namespace Mailosaur\Models;


class PreviewRequestOptions
{
    /**
     * @var PreviewRequest[] The list of email preview requests.
     */
    public $previews = array();

    public function __construct($previews)
    {
        $this->previews = $previews;
    }

    public function __toArray()
    {
        $model = array(
            'previews'      => $this->previews
        );

        return $model;
    }

    /**
     * Prepare json-serialized string
     *
     * @return string
     */
    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}
