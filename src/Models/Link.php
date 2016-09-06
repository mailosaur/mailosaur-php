<?php namespace Mailosaur\Models;


class Link
{
    /**
     * @var string
     */
    public $href;

    /**
     * @var string
     */
    public $text;

    public function __construct(\stdClass $link = null)
    {
        if ($link !== null) {
            if (property_exists($link, 'href')) {
                $this->href = $link->href;
            }

            if (property_exists($link, 'text')) {
                $this->text = $link->text;
            }
        }
    }
}