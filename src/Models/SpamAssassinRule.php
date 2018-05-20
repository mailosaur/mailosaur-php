<?php

namespace Mailosaur\Models;


class SpamAssassinRule
{
    /** @var double */
    public $score;

    /** @var string */
    public $rule;

    /** @var string */
    public $description;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'score')) {
            $this->score = $data->score;
        }

        if (property_exists($data, 'rule')) {
            $this->rule = $data->rule;
        }

        if (property_exists($data, 'description')) {
            $this->description = $data->description;
        }
    }
}