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

        curl_setopt($curl, CURLOPT_USERAGENT, 'mailosaur-php/7.0.1');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client->getApiKey() . ':');

        $response     = curl_exec($curl);
        $requestState = curl_getinfo($curl);
        $message      = '';

        if ($requestState['http_code'] != 200 && $requestState['http_code'] != 204) {
            switch ($requestState['http_code']) {
                case 400:
                    try {
                        $json = json_decode($response);
                        foreach ($json->{'errors'} as &$err) {
                            $message .= '(' . $err->field . ') ' . $err->detail[0]->description . '\r\n';
                        }
                    } catch (Exception $ex) {
                        $message = 'Request had one or more invalid parameters.';
                    }
                    throw new MailosaurException($message, 'invalid_request', $requestState['http_code'], $response);
                case 401:
                    throw new MailosaurException('Authentication failed, check your API key.', 'authentication_error', $requestState['http_code'], $response);
                case 403:
                    throw new MailosaurException('Insufficient permission to perform that task.', 'permission_error', $requestState['http_code'], $response);
                case 404:
                    throw new MailosaurException('Not found, check input parameters.', 'invalid_request', $requestState['http_code'], $response);
                default:
                    throw new MailosaurException('An API error occurred, see httpResponse for further information.', 'api_error', $requestState['http_code'], $response);
            }
        }

        return $response;
    }
}
