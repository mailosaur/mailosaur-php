<?php

namespace Mailosaur\Models;


class DeliverabilityReport
{
    /** @var \Mailosaur\Models\EmailAuthenticationResult */
    public $spf;

    /** @var \Mailosaur\Models\EmailAuthenticationResult[] */
    public $dkim;

    /** @var \Mailosaur\Models\EmailAuthenticationResult */
    public $dmarc;

    /** @var \Mailosaur\Models\BlockListResult[] */
    public $blockLists = array();

    /** @var \Mailosaur\Models\Content */
    public $content;

    /** @var \Mailosaur\Models\DnsRecords */
    public $dnsRecords;

    /** @var \Mailosaur\Models\SpamAssassinResult */
    public $spamAssassin;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'spf')) {
            $this->spf = new EmailAuthenticationResult($data->spf);
        }

        if (property_exists($data, 'dkim') && is_array($data->dkim)) {
            foreach ($data->dkim as $dkimResult) {
                $this->dkim[] = new EmailAuthenticationResult($dkimResult);
            }
        }

        if (property_exists($data, 'dmarc')) {
            $this->dmarc = new EmailAuthenticationResult($data->dmarc);
        }

        if (property_exists($data, 'blockLists') && is_array($data->blockLists)) {
            foreach ($data->blockLists as $blockList) {
                $this->blockLists[] = new BlockListResult($blockList);
            }
        }

        if (property_exists($data, 'content')) {
            $this->content = new Content($data->content);
        }

        if (property_exists($data, 'dnsRecords')) {
            $this->dnsRecords = new DnsRecords($data->dnsRecords);
        }

        if (property_exists($data, 'spamAssassin')) {
            $this->spamAssassin = new SpamAssassinResult($data->spamAssassin);
        }
    }
}