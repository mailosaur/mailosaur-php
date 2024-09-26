<?php

namespace Mailosaur\Models;


class SpamAssassinResult
{
    /** @var double */
    public $score;

    /** @var \Mailosaur\Models\ResultEnum */
    public $result;

    /** @var \Mailosaur\Models\SpamAssassinRule[] */
    public $rules = array();

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'score')) {
            $this->score = $data->score;
        }

        if (property_exists($data, 'result')) {
            $this->result = ResultEnum::from($data->result);
        }
        
        if (property_exists($data, 'rules') && is_array($data->rules)) {
            foreach ($data->rules as $rule) {
                $this->rules[] = new SpamAssassinRule($rule);
            }
        }
    }
}