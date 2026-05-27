<?php

namespace Mailosaur;


use Mailosaur\Operations\Analysis;
use Mailosaur\Operations\Files;
use Mailosaur\Operations\Messages;
use Mailosaur\Operations\Servers;
use Mailosaur\Operations\Usage;
use Mailosaur\Operations\Devices;
use Mailosaur\Operations\Previews;

/**
 * The Mailosaur client — the main entry point to the Mailosaur API. Construct an instance with
 * your API key (or set the `MAILOSAUR_API_KEY` environment variable), then use the operations
 * namespaces (`messages`, `servers`, `files`, `analysis`, `usage`, `devices`, `previews`) to
 * automate email and SMS testing.
 */
class MailosaurClient
{
    /** @var string */
    protected $apiKey;

    /** @var string The base URI of the service. */
    protected $baseUri;

    /**
     * Operations for finding, retrieving, creating, and managing email and SMS messages.
     *
     * @var Operations\Messages
     */
    public $messages;

    /**
     * Operations for creating and managing your Mailosaur servers (virtual inboxes).
     *
     * @var Operations\Servers
     */
    public $servers;

    /**
     * Operations for downloading attachments, EML source, and email preview screenshots.
     *
     * @var Operations\Files
     */
    public $files;

    /**
     * Operations for analyzing email content and deliverability, including spam scoring.
     *
     * @var Operations\Analysis
     */
    public $analysis;

    /**
     * Operations for inspecting account usage limits and recent transactional usage.
     *
     * @var Operations\Usage
     */
    public $usage;

    /**
     * Operations for managing virtual security devices and retrieving their one-time passwords.
     *
     * @var Operations\Devices
     */
    public $devices;

    /**
     * Operations for discovering the email clients available for generating email previews.
     *
     * @var Operations\Previews
     */
    public $previews;


    /**
     * Returns an instance of the Mailosaur client.
     *
     * @param string $apiKey  Optional API key. Overrides the MAILOSAUR_API_KEY environment variable if set.
     * @param string $baseUri Optionally overrides the base URL of the Mailosaur service.
     *
     * @throws Models\MailosaurException If no API key can be resolved.
     */
    public function __construct($apiKey = null, $baseUri = 'https://mailosaur.com/')
    {
        $resolvedApiKey = $apiKey ?: getenv('MAILOSAUR_API_KEY');

        if (empty($resolvedApiKey)) {
            throw new Models\MailosaurException(
                "'apiKey' must be set via the MAILOSAUR_API_KEY environment variable, or passed to the MailosaurClient constructor.",
                'missing_api_key'
            );
        }

        $this->setApiKey($resolvedApiKey);

        $this->setBaseUri($baseUri);

        $this->messages = new Messages($this);

        $this->servers = new Servers($this);

        $this->files = new Files($this);

        $this->analysis = new Analysis($this);

        $this->usage = new Usage($this);

        $this->devices = new Devices($this);

        $this->previews = new Previews($this);
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
