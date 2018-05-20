<?php

namespace Mailosaur\Models;


class MailosaurError
{
    public $type;
    public $messages;
    public $parameters;
    public $model;

    public function __construct($data)
    {
        if (property_exists($data, 'type')) {
            $this->type = $data->type;
        }
        if (property_exists($data, 'messages')) {
            $this->messages = $data->messages;
        }
        if (property_exists($data, 'parameters')) {
            $this->parameters = $data->parameters;
        }
        if (property_exists($data, 'model')) {
            $this->model = $data->model;
        }
    }
}