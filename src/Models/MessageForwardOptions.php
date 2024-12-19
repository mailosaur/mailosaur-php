<?php

namespace Mailosaur\Models;


class MessageForwardOptions
{
    /**
     * @var string The email address to which the email will be sent.
     */
    public $to = null;

    /**
     * @var string The email address to which the email will be CC'd.
     */
    public $cc = null;

    /**
     * @var string Any additional plain text content to forward the email with. Note that only text or html can be supplied, not both.
     */
    public $text = null;

    /**
     * @var string Any additional HTML content to forward the email with. Note that only html or text can be supplied, not both.
     */
    public $html = null;

    public function __toArray()
    {
        return array(
            'to'  => $this->to,
            'cc'  => $this->cc,
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