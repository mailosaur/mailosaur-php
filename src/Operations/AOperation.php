<?php

namespace Mailosaur\Operations;


use Mailosaur\MailosaurClient;
use Mailosaur\Models\MailosaurException;

abstract class AOperation
{
    /** @var MailosaurClient */
    protected $client;

    public function __construct(MailosaurClient $client)
    {
        $this->client = $client;
    }

    /**
     * Perform request to api
     *
     * @param string $path    api path
     * @param array  $options additional curl options to set
     *
     * @return string
     * @throws \Mailosaur\Models\MailosaurException
     */
    protected function request($path, array $options = array())
    {
        $curl = curl_init($this->client->getBaseUri() . $path);

        if (count($options) > 0) {
            foreach ($options as $name => $value) {
                curl_setopt($curl, $name, $value);
            }
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client->getApiKey() . ':');

        $response     = curl_exec($curl);
        $requestState = curl_getinfo($curl);

        if ($requestState['http_code'] != 200 && $requestState['http_code'] != 204) {
            throw new MailosaurException(
                $response,
                $requestState['http_code']
            );
        }

        return $response;
    }
}