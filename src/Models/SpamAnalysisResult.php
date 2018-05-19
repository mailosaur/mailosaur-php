<?php

namespace Mailosaur\Models;


class SpamAnalysisResult
{
    /** @var \Mailosaur\Models\SpamFilterResults */
    public $spamFilterResults;

    /** @var double */
    public $score;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'spamFilterResults')) {
            $this->spamFilterResults = new SpamFilterResults($data->spamFilterResults);
        }

        if (property_exists($data, 'score')) {
            $this->score = $data->score;
        }
    }
}