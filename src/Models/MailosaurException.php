<?php

namespace Mailosaur\Models;


class MailosaurException extends \Exception
{
    public $errorType;
    public $httpStatusCode;
    public $httpResponseBody;

    public function __construct($message, $errorType, $httpStatusCode = null, $httpResponseBody = null)
    {
        parent::__construct($message, 0, null);
        $this->errorType = $errorType;
        $this->httpStatusCode = $httpStatusCode;
        $this->httpResponseBody = $httpResponseBody;
    }
}