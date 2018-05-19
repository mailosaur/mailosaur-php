<?php

namespace Mailosaur\Models;


class Message
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var MessageAddress[] The sender of the message.
     */
    public $from = array();

    /**
     * @var MessageAddress[] The message’s recipient.
     */
    public $to = array();

    /**
     * @var MessageAddress[] Carbon-copied recipients for email messages.
     */
    public $cc = array();

    /**
     * @var MessageAddress[] Blind carbon-copied recipients for email messages.
     */
    public $bcc = array();

    /**
     * @var \DateTime The datetime that this message was received by Mailosaur.
     */
    public $received;

    /**
     * @var string The message’s subject.
     */
    public $subject;

    /**
     * @var MessageContent
     */
    public $html;

    /**
     * @var MessageContent
     */
    public $text;

    /**
     * @var Attachment[] An array of attachment metadata for any attached files.
     */
    public $attachments = array();

    /**
     * @var Metadata Advanced use case content related to the message.
     */
    public $metadata;

    /**
     * @var string Identifier for the server in which the message is located.
     */
    public $server;

    public function __construct($data)
    {
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }

        if (property_exists($data, 'from') && is_array($data->from)) {
            foreach ($data->from as $from) {
                $this->from[] = new MessageAddress($from);
            }
        }

        if (property_exists($data, 'to') && is_array($data->to)) {
            foreach ($data->to as $to) {
                $this->to[] = new MessageAddress($to);
            }
        }

        if (property_exists($data, 'cc') && is_array($data->cc)) {
            foreach ($data->cc as $cc) {
                $this->cc[] = new MessageAddress($cc);
            }
        }

        if (property_exists($data, 'bcc') && is_array($data->bcc)) {
            foreach ($data->bcc as $bcc) {
                $this->bcc[] = new MessageAddress($bcc);
            }
        }

        if (property_exists($data, 'received')) {
            $this->received = new \DateTime($data->received);
        }

        if (property_exists($data, 'subject')) {
            $this->subject = $data->subject;
        }

        if (property_exists($data, 'html')) {
            $this->html = new MessageContent($data->html);
        }

        if (property_exists($data, 'text')) {
            $this->text = new MessageContent($data->text);
        }

        if (property_exists($data, 'attachments') && is_array($data->attachments)) {
            foreach ($data->attachments as $attachment) {
                $this->attachments[] = new Attachment($attachment);
            }
        }

        if (property_exists($data, 'metadata')) {
            $this->metadata = new Metadata($data->metadata);
        }

        if (property_exists($data, 'server')) {
            $this->server = $data->server;
        }
    }
}