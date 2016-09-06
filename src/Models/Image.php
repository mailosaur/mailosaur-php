<?php namespace Mailosaur\Models;


class Image
{
    /**
     * @var string
     */
    public $src;

    /**
     * @var string
     */
    public $alt;

    public function __construct(\stdClass $image = null)
    {
        if ($image !== null) {
            if (property_exists($image, 'src')) {
                $this->src = $image->src;
            }

            if (property_exists($image, 'alt')) {
                $this->alt = $image->alt;
            }
        }
    }
}