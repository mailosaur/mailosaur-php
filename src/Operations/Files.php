<?php

namespace Mailosaur\Operations;

use Mailosaur\Models\MailosaurException;

/**
 * Operations for downloading the raw content associated with a message — file attachments, the
 * full EML source of an email, and rendered email previews. Accessed via `client->files`.
 */
class Files extends AOperation
{
    /**
     * <strong>Download an attachment</strong>
     * <p>Downloads a single attachment.</p>
     *
     * @param string $id The identifier of the attachment to be downloaded.
     *
     * @return string The attachment's binary content.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Files_GetAttachment Download an attachment documentation
     * @example https://mailosaur.com/docs/api/#operation/Files_GetAttachment
     */
    public function getAttachment($id)
    {
        return $this->request('api/files/attachments/' . urlencode($id));
    }

    /**
     * <strong>Download EML</strong>
     * <p>Downloads an EML file representing the specified email.<br />
     * Simply supply the unique identifier for the required email.</p>
     *
     * @param string $id The identifier of the email to be downloaded.
     *
     * @return string The raw EML content of the email.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Files_GetEmail Download EML documentation
     * @example https://mailosaur.com/docs/api/#operation/Files_GetEmail
     */
    public function getEmail($id)
    {
        return $this->request('api/files/email/' . urlencode($id));
    }

    /**
     * <strong>Download an email preview</strong>
     * <p>Downloads a screenshot of your email rendered in a real email client. Simply supply the
     * unique identifier for the required preview.</p>
     *
     * @param string $id The identifier of the email preview to be downloaded.
     *
     * @return string The preview screenshot image content.
     * @throws \Mailosaur\Models\MailosaurException With error type `preview_timeout` if the preview is not
     *                                              generated within the time limit.
     */
    public function getPreview($id)
    {
        $timeout = 120000;
        $pollCount = 0;
        $headers = [];
        $startTime = round(microtime(true) * 1000);

        while (true) {
            $path = 'api/files/screenshots/' . urlencode($id);
            
            // Make custom curl call to handle 202 responses (similar to search method)
            $curl = curl_init($this->client->getBaseUri() . $path);
            
            $this->setupStandardCurlOptions($curl);
            curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;
    
                $headers[strtolower(trim($header[0]))][] = trim($header[1]);
    
                return $len;
            });

            $response = curl_exec($curl);
            $requestState = curl_getinfo($curl);

            // Return if successful
            if ($requestState['http_code'] == 200) {
                return $response;
            }

            // If not 202 (Accepted), handle error
            if ($requestState['http_code'] != 202) {
                $this->handleHttpError($requestState['http_code'], $response);
            }

            $delayPattern = isset($headers['x-ms-delay']) ?
                array_map('intval', explode(',', $headers['x-ms-delay'][0] ?: '1000'))
                : array(1000);

            $delay = $pollCount >= count($delayPattern) ?
                $delayPattern[count($delayPattern) - 1] :
                $delayPattern[$pollCount];

            $pollCount++;

            // Stop if timeout will be exceeded
            if ((round(microtime(true) * 1000) - $startTime) + $delay > $timeout) {
                throw new MailosaurException("An email preview was not generated in time. The email client may not be available, or the preview ID [" . $id . "] may be incorrect.", "preview_timeout");
            }

            sleep($delay / 1000);
        }
    }
}
