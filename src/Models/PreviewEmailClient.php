<?php

namespace Mailosaur\Models;


class PreviewEmailClient
{
    /** @var string The unique identifier for the email preview. */
    public $id;

    /** @var string The display name of the email client. */
    public $name;

    /** @var string Whether the platform is desktop, mobile, or web-based. */
    public $platformGroup;

    /** @var string The type of platform on which the email client is running. */
    public $platformType;

    /** @var string The platform version number. */
    public $platformVersion;

    /** @var bool Whether images can be disabled when generating previews. */
    public $canDisableImages;

    /** @var string The current status of the email client. */
    public $status;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'name')) {
            $this->name = $data->name;
        }
        
        if (property_exists($data, 'platformGroup')) {
            $this->platformGroup = $data->platformGroup;
        }

        if (property_exists($data, 'platformType')) {
            $this->platformType = $data->platformType;
        }

        if (property_exists($data, 'platformVersion')) {
            $this->platformVersion = $data->platformVersion;
        }

        if (property_exists($data, 'canDisableImages')) {
            $this->canDisableImages = $data->canDisableImages;
        }

        if (property_exists($data, 'status')) {
            $this->status = $data->status;
        }
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $model = array(
            'id'                => $this->id,
            'name'              => $this->name,
            'platformGroup'     => $this->platformGroup,
            'platformType'      => $this->platformType,
            'platformVersion'   => $this->platformVersion,
            'canDisableImages'  => $this->canDisableImages,
            'status'            => $this->status
        );

        return $model;
    }

    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}
