<?php

namespace Mailosaur\Models;


class MessageReplyOptions
{
    /**
     * @var string Any additional plain text content to include in the reply. Note that only text or html can be supplied, not both.
     */
    public $text = null;

    /**
     * @var string Any additional HTML content to include in the reply. Note that only html or text can be supplied, not both.
     */
    public $html = null;

    public function __toArray()
    {
        return array(
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