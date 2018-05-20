<?php

namespace Mailosaur\Models;


class MailosaurException extends \Exception
{
    protected $error;

    public function __construct($message = '', $code = 0, $previous = null)
    {
        $errorDetails = json_decode($message);
        $this->code   = $code;

        if ($code == 401) {
            $this->message = "Operation returned an invalid status code 'Unauthorised'";
        } elseif ($code == 404) {
            $this->message = "Operation returned an invalid status code 'Not Found'";
        } elseif ($code == 400 && !empty($errorDetails)) {
            $this->message = "Operation returned an invalid status code 'Bad Request'";
            $this->error   = new MailosaurError($errorDetails);
        } else {
            $this->message = 'Unspecified error.';
        }
    }

    /**
     * @return \Mailosaur\Models\MailosaurError
     */
    public function getError()
    {
        return $this->error;
    }
}