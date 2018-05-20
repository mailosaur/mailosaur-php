<?php

namespace Mailosaur\Models;


class SearchCriteria
{
    /**
     * @var string The full email address to which the target email was sent.
     */
    public $sentTo = null;

    /**
     * @var string The value to seek within the target email's subject line.
     */
    public $subject = null;

    /**
     * @var string The value to seek within the target email's HTML or text body.
     */
    public $body = null;

    public function __toArray()
    {
        return array(
            'sentTo'  => $this->sentTo,
            'subject' => $this->subject,
            'body'    => $this->body,
        );
    }

    /**
     * Prepare json-serialized string of search criteria
     *
     * @return string
     */
    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}