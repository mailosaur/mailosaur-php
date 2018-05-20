<?php

namespace Mailosaur\Models;


class Attachment
{
    /** @var string */
    public $id;

    /** @var string */
    public $contentType;

    /** @var string */
    public $fileName;

    /** @var string */
    public $contentId;

    /** @var int */
    public $length;

    /** @var \DateTime */
    public $creationDate;

    /** @var string */
    public $url;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'contentType')) {
            $this->contentType = $data->contentType;
        }

        if (property_exists($data, 'fileName')) {
            $this->fileName = $data->fileName;
        }

        if (property_exists($data, 'contentId')) {
            $this->contentId = $data->contentId;
        }

        if (property_exists($data, 'length')) {
            $this->length = $data->length;
        }

        if (property_exists($data, 'creationDate')) {
            $this->creationDate = new \DateTime($data->creationDate);
        }

        if (property_exists($data, 'url')) {
            $this->url = $data->url;
        }
    }
}