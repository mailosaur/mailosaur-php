<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 5/16/2018
 * Time: 4:12 PM
 */

namespace Mailosaur\Operations;


use Mailosaur\Models\Server;
use Mailosaur\Models\ServerCreateOptions;
use Mailosaur\Models\ServerListResult;

/**
 * Operations for creating and managing your Mailosaur servers — the virtual inboxes that group
 * your tests together, each with its own domain and SMTP/POP3/IMAP credentials. Accessed via
 * `client->servers`.
 */
class Servers extends AOperation
{

    /**
     * <strong>List all servers</strong>
     * <p>Returns a list of your virtual servers. Servers are returned sorted in alphabetical order.</p>
     *
     * @return ServerListResult Your servers.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_List List all servers
     * @example https://mailosaur.com/docs/api/#operation/Servers_List
     */
    public function all()
    {
        $response = $this->request('api/servers');

        $response = json_decode($response);

        return new ServerListResult($response);
    }

    /**
     * <strong>Create a server</strong>
     * <p>Creates a new virtual server and returns it.</p>
     *
     * @param ServerCreateOptions $serverCreateOptions Options used to create a new Mailosaur server.
     *
     * @return \Mailosaur\Models\Server The newly-created server.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Create Create a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Create
     */
    public function create(ServerCreateOptions $serverCreateOptions)
    {
        $payload = $serverCreateOptions->toJsonString();

        $response = $this->request(
            'api/servers/',
            array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS    => $payload,
                CURLOPT_HTTPHEADER    => array('Content-Type:application/json', 'Content-Length: ' . strlen($payload))
            )
        );

        $response = json_decode($response);

        return new Server($response);
    }

    /**
     * <strong>Retrieve a server</strong>
     * <p>Retrieves the detail for a single server.</p>
     *
     * @param string $id The unique identifier of the server to be retrieved.
     *
     * @return \Mailosaur\Models\Server The server.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Get Retrieve a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Get
     */
    public function get($id)
    {
        $response = $this->request('api/servers/' . urlencode($id));

        $response = json_decode($response);

        return new Server($response);
    }

    /**
     * <strong>Retrieve server password</strong>
     * <p>Retrieves the password for a server. This password can be used for SMTP, POP3, and IMAP connectivity.</p>
     *
     * @param string $id The unique identifier of the server.
     *
     * @return string The server's password.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Get_Password Retrieve server password
     * @example https://mailosaur.com/docs/api/#operation/Servers_Get_Password
     */
    public function getPassword($id)
    {
        $response = $this->request('api/servers/' . urlencode($id) . '/password');

        $response = json_decode($response);

        return $response->value;
    }

    /**
     * <strong>Update a server</strong>
     * <p>Updates the attributes of a server and returns it.</p>
     *
     * @param string                   $id     The unique identifier of the server to be updated.
     * @param \Mailosaur\Models\Server $server The updated server.
     *
     * @return \Mailosaur\Models\Server The updated server.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Update Update a server docs
     * @example https://mailosaur.com/docs/api/#operation/Servers_Update
     */
    public function update($id, Server $server)
    {
        $payload = $server->toJsonString();

        $response = $this->request(
            'api/servers/' . urlencode($id),
            array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS    => $payload,
                CURLOPT_HTTPHEADER    => array('Content-Type:application/json', 'Content-Length: ' . strlen($payload))
            )
        );

        $response = json_decode($response);

        return new Server($response);
    }

    /**
     * <strong>Delete a server</strong>
     * <p>Permanently deletes a server. This will also delete all messages, associated attachments,
     * etc. within the server. This operation cannot be undone.</p>
     *
     * @param string $id The unique identifier of the server to be deleted.
     *
     * @return void
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Delete Delete a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Delete
     */
    public function delete($id)
    {
        $this->request('api/servers/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    /**
     * <strong>Generate a random email address</strong>
     * <p>Generates a random email address by appending a random string in front of the server's
     * domain name.</p>
     *
     * @param string $server The identifier of the server.
     *
     * @return string A random email address ending in the server's domain.
     */
    public function generateEmailAddress($server)
    {
        $host = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.net';

        return self::randomString(10) . '@' . $server . '.' . $host;
    }

    public static function randomString($length)
    {
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length);
    }
}
