<?php

namespace Mailosaur\Models;


class Server
{
    /** @var string Unique identifier for the server. Used as username for SMTP/POP3 authentication. */
    public $id;

    /** @var string SMTP/POP3 password. */
    public $password;

    /** @var string A name used to identify the server. */
    public $name;

    /** @var array Users (excluding administrators) who have access to the server. */
    public $users = array();

    /** @var int The number of messages currently in the server. */
    public $messages;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'password')) {
            $this->password = $data->password;
        }

        if (property_exists($data, 'name')) {
            $this->name = $data->name;
        }

        if (property_exists($data, 'users') && is_array($data->users)) {
            $this->users = $data->users;
        }

        if (property_exists($data, 'messages')) {
            $this->messages = $data->messages;
        }
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $serverInfo = array(
            'id'              => $this->id,
            'password'        => $this->password,
            'name'            => $this->name,
            'users'           => $this->users,
            'messages'        => $this->messages
        );

        return $serverInfo;
    }

    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}