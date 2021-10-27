<?php

namespace Mailosaur\Models;


class MessageCreateOptions
{
    /**
     * @var string The email address to which the email will be sent.
     */
    public $to = null;

    /**
     * @var bool If true, email will be sent upon creation.
     */
    public $send = null;

    /**
     * @var string The email subject line.
     */
    public $subject = null;

    /**
     * @var string The plain text body of the email. Note that only text or html can be supplied, not both.
     */
    public $text = null;

    /**
     * @var string The HTML body of the email. Note that only text or html can be supplied, not both.
     */
    public $html = null;

    public function __toArray()
    {
        return array(
            'to'  => $this->to,
            'send'  => $this->send,
            'subject' => $this->subject,
            'text'    => $this->text,
            'html'    => $this->html,
        );
    }

    /**
     * Prepare json-serialized string
     *
     * @return string
     */
    public function toJsonString()
    {
        return json_encode($this->__toArray());
    }
}