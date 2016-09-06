<?php namespace Mailosaur;

use Mailosaur\Models\Email;

/**
 * Class Client
 *
 * @package Mailosaur
 * @see     https://mailosaur.com/docs documentation
 */
class Client
{

    /**
     * @var string
     */
    protected $mailbox;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $apiUrl = 'https://mailosaur.com/api';

    /**
     * Client constructor.
     *
     * @param string $key     api key
     * @param string $mailbox mailbox
     * @param string $apiUrl  (optional) api base url
     *
     * @see https://mailosaur.com/docs/email#mailboxid Authentication & Mailbox ID documentation
     */
    public function __construct($key, $mailbox, $apiUrl = null)
    {
        $this->mailbox = $mailbox;
        $this->key     = $key;

        if ($apiUrl !== null) {
            $this->apiUrl = $apiUrl;
        }
    }

    /**
     * <strong>Get email by id</strong>
     *
     * @param string $id email id
     *
     * @return \Mailosaur\Models\Email
     */
    public function getEmail($id)
    {
        $email = $this->request('/emails/' . urlencode($id));
        $email = json_decode($email);

        return Models\Email::fillFromResponse($email);
    }

    /**
     * <strong>List all email</strong><br/>
     * Returns a list of emails in this mailbox.<br/>
     * The email returned is sorted by receipt date, with the most recent email appearing first.
     *
     * @return Models\Email[]
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#list-email List all email documentation
     * @example https://mailosaur.com/docs/email#list-email
     */
    public function getEmails()
    {
        $emails = $this->request('/mailboxes/' . $this->mailbox . '/emails');
        $emails = json_decode($emails);

        return $this->wrapEmails($emails);
    }

    /**
     * <strong>Get all emails for recipient</strong><br/>
     * You’ll usually know what address you send a mail to.<br/>
     * Use can use this information to return matches of that address.
     *
     * @param string $recipient recipient email address
     *
     * @return Models\Email[]
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#list-email-recipient List email by recipient documentation
     * @example https://mailosaur.com/docs/email#list-email-recipient
     */
    public function getEmailsByRecipient($recipient)
    {
        $emails = $this->request('/mailboxes/' . $this->mailbox . '/emails?recipient=' . urlencode($recipient));
        $emails = json_decode($emails);

        return $this->wrapEmails($emails);
    }

    /**
     * <strong>List email by search pattern</strong><br/>
     * These examples show you how to fetch all email where the body or subject matches the search pattern provided.
     *
     * @param string $pattern search pattern
     *
     * @return Models\Email[]
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#list-email-search List email by search pattern documentation
     * @example https://mailosaur.com/docs/email#list-email-search
     */
    public function getEmailsBySearchPattern($pattern)
    {
        $emails = $this->request('/mailboxes/' . $this->mailbox . '/emails?search=' . urlencode($pattern));
        $emails = json_decode($emails);

        return $this->wrapEmails($emails);
    }

    /**
     * Downloads an attachment.
     *
     * @param string $attachmentId attachment id
     *
     * @return string
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#attachment Downloads an attachment documentation
     * @example https://mailosaur.com/docs/email#attachment
     */
    public function getAttachment($attachmentId)
    {
        return $this->request('/attachments/' . urlencode($attachmentId));
    }

    /**
     * Download EML
     *
     * @param string $id email id
     *
     * @return string
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#eml Download EML documentation
     * @example https://mailosaur.com/docs/email#eml
     */
    public function getEML($id)
    {
        return $this->request('/raw/' . urlencode($id));
    }

    /**
     * Delete email by id
     *
     * @param string $id email id
     *
     * @return void
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#delete Delete an email documentation
     * @example https://mailosaur.com/docs/email#delete
     */
    public function deleteEmail($id)
    {
        $this->request('/emails/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    /**
     * <strong>Empty mailbox</strong><br/>
     * Permanently deletes all email in the specified mailbox.<br/>
     * This cannot be undone.
     *
     * @return void
     * @throws \Mailosaur\Exception
     * @see     https://mailosaur.com/docs/email#empty Empty mailbox documentation
     * @example https://mailosaur.com/docs/email#empty
     */
    public function emptyMailBox()
    {
        $this->request('/mailboxes/' . $this->mailbox . '/empty/', array(CURLOPT_CUSTOMREQUEST => 'POST'));
    }

    /**
     * Perform request to api
     *
     * @param string $path    api path
     * @param array  $options additional curl options to set
     *
     * @return string
     * @throws \Mailosaur\Exception
     */
    protected function request($path, array $options = array())
    {
        $curl = curl_init($this->apiUrl . $path);

        if (count($options) > 0) {
            foreach ($options as $name => $value) {
                curl_setopt($curl, $name, $value);
            }
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $this->key . ':');

        $response     = curl_exec($curl);
        $requestState = curl_getinfo($curl);

        if ($requestState['http_code'] != 200 && $requestState['http_code'] != 204) {
            throw new Exception('Bad request. Check your credentials.');
        }

        return $response;
    }

    /**
     * Create email objects from response data
     *
     * @param array $responseData
     *
     * @return Email[]
     */
    public function wrapEmails(array $responseData)
    {
        $emails = array();

        foreach ($responseData as $email) {
            $emails[] = Models\Email::fillFromResponse($email);
        }

        return $emails;
    }

    /**
     * Generate random email address
     *
     * @return string
     */
    public function generateEmailAddress()
    {
        return mt_rand(0, 1000000) . '-' . mt_rand(2000000, 4000000) . '.' . $this->mailbox . '@mailosaur.io';
    }
}