<?php


namespace app\src\form;


use app\src\Model;

abstract class BaseField
{
    public Model $model;
    public string $attribute;


    /**
     * InputField constructor.
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderInput(): string;


    public function __toString()
    {
        return sprintf('
            <div class="form-group">
                <label>%s</label>
                %s
                <div class="invalid-feedback">%s</div>
            </div>
        ',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}