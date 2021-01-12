<?php


namespace app\src\form;


use app\src\Model;

class SelectField extends BaseField
{
    private array $options = [];
    private bool $multiple = false;

    public function __construct(Model $model, string $attribute, array $options, bool $multiple)
    {
        parent::__construct($model, $attribute);

        // options need to be in the form of [['id' => X, 'value' => Y]]
        $this->options = $options;
        $this->multiple = $multiple;
    }

    public function renderInput(): string
    {
        $field = '<select name="%s" id="%s-select" %s>';
        foreach ($this->options as $option) {
            $selected = "";
            if (is_array($this->model->{$this->attribute})){
                if (in_array($option['id'], $this->model->{$this->attribute})){
                    $selected = "selected";
                }
            } else {
                if ($option['id'] === $this->model->{$this->attribute}) {
                    $selected = "selected";
                }
            }

            $field .= "<option value=" . $option['id'] ." $selected>" . $option['value'] . "</option>";
        }
        $field .= '</select>';

        return sprintf($field,
            $this->attribute,
            $this->attribute,
            $this->multiple ? 'multiple' : ""

        );
    }
}