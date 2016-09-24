<?php namespace Mailosaur\Models;


class Email
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $rawId;

    /**
     * @var \DateTime
     */
    public $creationDate;

    /**
     * @var string
     */
    public $senderHost;

    /**
     * @var string
     */
    public $mailbox;

    /**
     * @var EmailAddress[]
     */
    public $from = array();

    /**
     * @var EmailAddress[]
     */
    public $to = array();

    /**
     * @var string
     */
    public $html;

    /**
     * @var string
     */
    public $text;

    /**
     * @var Link[]
     */
    public $htmlLinks = array();

    /**
     * @var Link[]
     */
    public $textLinks = array();

    /**
     * @var Image[]
     */
    public $images = array();

    /**
     * @var \stdClass
     */
    public $headers;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $priority;

    /**
     * @var Attachment[]
     */
    public $attachments = array();

    /**
     * Create Email object from raw response entry
     *
     * @param \stdClass $response
     *
     * @return \Mailosaur\Models\Email
     */
    public static function fillFromResponse(\stdClass $response)
    {
        $email = new self;

        if (property_exists($response, 'id')) {
            $email->id = $response->id;
        }

        if (property_exists($response, 'rawid')) {
            $email->rawId = $response->rawid;
        }

        if (property_exists($response, 'creationdate')) {
            $email->creationDate = new \DateTime($response->creationdate);
        }

        if (property_exists($response, 'senderhost')) {
            $email->senderHost = $response->senderhost;
        }

        if (property_exists($response, 'mailbox')) {
            $email->mailbox = $response->mailbox;
        }

        if (property_exists($response, 'from') && is_array($response->from) && count($response->from) > 0) {
            foreach ($response->from as $from) {
                $email->from[] = new EmailAddress($from);
            }
        }

        if (property_exists($response, 'to') && is_array($response->to) && count($response->to) > 0) {
            foreach ($response->to as $to) {
                $email->to[] = new EmailAddress($to);
            }
        }

        if (property_exists($response, 'html')) {
            if (property_exists($response->html, 'body')) {
                $email->html = $response->html->body;
            }

            if (property_exists($response->html, 'links') && is_array($response->html->links) && count($response->html->links) > 0) {
                foreach ($response->html->links as $link) {
                    $email->htmlLinks[] = new Link($link);
                }
            }

            if (property_exists($response->html, 'images') && is_array($response->html->images) && count($response->html->images) > 0) {
                foreach ($response->html->images as $image) {
                    $email->images[] = new Image($image);
                }
            }
        }

        if (property_exists($response, 'text')) {
            if (property_exists($response->text, 'body')) {
                $email->text = $response->text->body;
            }

            if (property_exists($response->text, 'links') && is_array($response->text->links) && count($response->text->links) > 0) {
                foreach ($response->text->links as $link) {
                    $email->textLinks[] = new Link($link);
                }
            }
        }

        if (property_exists($response, 'headers')) {
            $email->headers = clone $response->headers;
        }

        if (property_exists($response, 'subject')) {
            $email->subject = $response->subject;
        }

        if (property_exists($response, 'priority')) {
            $email->priority = $response->priority;
        }

        if (property_exists($response, 'attachments') && is_array($response->attachments) && count($response->attachments) > 0) {
            foreach ($response->attachments as $attachment) {
                $email->attachments[] = new Attachment($attachment);
            }
        }

        return $email;
    }

}