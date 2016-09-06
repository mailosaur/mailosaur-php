<?php namespace Mailosaur\Models;


class Attachment
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $contentType;

    /**
     * @var string
     */
    public $fileName;
    /**
     * @var string
     */
    public $contentId = null;

    /**
     * @var int
     */
    public $length;

    public function __construct(\stdClass $attachment = null)
    {
        if ($attachment !== null) {
            if (property_exists($attachment, 'id')) {
                $this->id = $attachment->id;
            }

            if (property_exists($attachment, 'contentType')) {
                $this->contentType = $attachment->contentType;
            }

            if (property_exists($attachment, 'fileName')) {
                $this->fileName = $attachment->fileName;
            }

            if (property_exists($attachment, 'length')) {
                $this->length = (int)$attachment->length;
            }

            if (property_exists($attachment, 'contentId')) {
                $this->contentId = $attachment->contentId;
            }
        }
    }

}