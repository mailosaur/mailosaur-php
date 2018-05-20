<?php

namespace Mailosaur\Models;


class ForwardingRule
{
    /** @var string Enum:"from" "to" "subject" */
    public $field;

    /** @var string Enum:"endsWith" "startsWith" "contains" */
    public $operator;

    /** @var string */
    public $value;

    /** @var string */
    public $forwardTo;

    public function __construct(\stdClass $data)
    {
        if (property_exists($data, 'field')) {
            $this->field = $data->field;
        }

        if (property_exists($data, 'operator')) {
            $this->operator = $data->operator;
        }

        if (property_exists($data, 'value')) {
            $this->value = $data->value;
        }

        if (property_exists($data, 'forwardTo')) {
            $this->forwardTo = $data->forwardTo;
        }
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return array(
            'field'     => $this->field,
            'operator'  => $this->operator,
            'value'     => $this->value,
            'forwardTo' => $this->forwardTo
        );
    }
}