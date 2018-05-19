<?php

namespace Mailosaur;


use Mailosaur\Operations\Analysis;
use Mailosaur\Operations\Files;
use Mailosaur\Operations\Messages;
use Mailosaur\Operations\Servers;

class MailosaurClient
{
    /** @var string */
    protected $apiKey;

    /** @var string The base URI of the service. */
    protected $baseUri;

    /** @var Operations\Messages */
    public $messages;

    /** @var Operations\Servers */
    public $servers;

    /** @var Operations\Files */
    public $files;

    /** @var Operations\Analysis */
    public $analysis;


    public function __construct($apiKey, $baseUri = 'https://mailosaur.com/')
    {
        $this->setApiKey($apiKey);

        $this->setBaseUri($baseUri);

        $this->messages = new Messages($this);

        $this->servers = new Servers($this);

        $this->files = new Files($this);

        $this->analysis = new Analysis($this);
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    protected function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

}