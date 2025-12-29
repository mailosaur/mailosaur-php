<?php

namespace Mailosaur\Models;


class PreviewRequestOptions
{
    /**
     * @var string[] The list email clients to generate previews with.
     */
    public $emailClients = array();

    public function __construct($emailClients)
    {
        $this->emailClients = $emailClients;
    }

    public function __toArray()
    {
        $model = array(
            'emailClients' => $this->emailClients
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
