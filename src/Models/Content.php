<?php

namespace Mailosaur\Models;


class Content
{
    /** @var bool */
    public $embed;

    /** @var bool */
    public $iframe;

    /** @var bool */
    public $object;

    /** @var bool */
    public $script;

    /** @var bool */
    public $shortUrls;

    /** @var int */
    public $textSize;

    /** @var int */
    public $totalSize;

    /** @var bool */
    public $missingAlt;

    /** @var bool */
    public $missingListUnsubscribe;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'embed')) {
            $this->embed = $data->embed;
        }

        if (property_exists($data, 'iframe')) {
            $this->iframe = $data->iframe;
        }

        if (property_exists($data, 'object')) {
            $this->object = $data->object;
        }

        if (property_exists($data, 'script')) {
            $this->script = $data->script;
        }

        if (property_exists($data, 'shortUrls')) {
            $this->shortUrls = $data->shortUrls;
        }

        if (property_exists($data, 'textSize')) {
            $this->textSize = $data->textSize;
        }

        if (property_exists($data, 'totalSize')) {
            $this->totalSize = $data->totalSize;
        }
        
        if (property_exists($data, 'missingAlt')) {
            $this->missingAlt = $data->missingAlt;
        }

        if (property_exists($data, 'missingListUnsubscribe')) {
            $this->missingListUnsubscribe = $data->missingListUnsubscribe;
        }
    }
}