<?php


namespace core\form;


class BooleanField extends RadioField
{
    public function __construct(\core\Model $model, string $attribute, array $labels = ['True', 'False'])
    {
        $options = [
            $labels[0] => true,
            $labels[1] => false
        ];

        parent::__construct($model, $attribute, $options);
    }
}