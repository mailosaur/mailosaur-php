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

class Servers extends AOperation
{

    /**
     * <strong>List all servers</strong>
     *
     * @return ServerListResult
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_List List all servers
     * @example https://mailosaur.com/docs/api/#operation/Servers_List
     */
    public function all()
    {
        $response = $this->request('/api/servers');

        $response = json_decode($response);

        return new ServerListResult($response);
    }

    /**
     * <strong>Create a server</strong>
     * <p>Creates a new virtual SMTP server and returns it.</p>
     *
     * @param $serverCreateOptions
     *
     * @return \Mailosaur\Models\Server
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Create Create a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Create
     */
    public function create(ServerCreateOptions $serverCreateOptions)
    {
        $payload = $serverCreateOptions->toJsonString();

        $response = $this->request(
            '/api/servers/',
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
     *
     * @param string $id The identifier of the server to be retrieved.
     *
     * @return \Mailosaur\Models\Server
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Get Retrieve a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Get
     */
    public function get($id)
    {
        $response = $this->request('/api/servers/' . urlencode($id));

        $response = json_decode($response);

        return new Server($response);
    }

    /**
     * <strong>Update a server</strong>
     * <p>Updates a single server and returns it.</p>
     *
     * @param string                   $id The identifier of the server to be updated.
     * @param \Mailosaur\Models\Server $server
     *
     * @return \Mailosaur\Models\Server
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Update Update a server docs
     * @example https://mailosaur.com/docs/api/#operation/Servers_Update
     */
    public function update($id, Server $server)
    {
        $payload = $server->toJsonString();

        $response = $this->request(
            '/api/servers/' . urlencode($id),
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
     *
     * @param string $id The identifier of the server to be deleted.
     *
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Servers_Delete Delete a server
     * @example https://mailosaur.com/docs/api/#operation/Servers_Delete
     */
    public function delete($id)
    {
        $this->request('/api/servers/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    public function generateEmailAddress($server)
    {
        $host = ($h = getenv('MAILOSAUR_SMTP_HOST')) ? $h : 'mailosaur.io';

        return self::randomString() . '.' . $server . '@' . $host;
    }

    public static function randomString()
    {
        $chars = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

        return substr($chars, rand(0, strlen($chars) - 10), 10);
    }
}