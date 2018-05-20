<?php

namespace Mailosaur\Models;


class SpamFilterResults
{
    /** @var \Mailosaur\Models\SpamAssassinRule[] */
    public $spamAssassin = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'spamAssassin') && is_array($data->spamAssassin)) {
            foreach ($data->spamAssassin as $rule) {
                $this->spamAssassin[] = new SpamAssassinRule($rule);
            }
        }
    }
}