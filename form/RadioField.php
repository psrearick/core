<?php

namespace core\form;

class RadioField extends BaseField
{
    protected array $options = [];


    public function __construct(\core\Model $model, string $attribute, array $options)
    {
        $this->options = $options;
        parent::__construct($model, $attribute);
    }

    public function renderInput(): string
    {
        $field = "";
        foreach ($this->options as $label => $value) {
            $id = $this->attribute . "_" . $value;
            $field .= sprintf("
                <div>
                    <input type='radio' id='%s' name='%s' value='%s' %s>
                    <label for='%s'>%s</label>
                </div>
            ",
            $id,
            $this->attribute,
            $value,
            $this->model->{$this->attribute} === $value,
            $id,
            $label
            );
        }
        return $field;
    }
}
