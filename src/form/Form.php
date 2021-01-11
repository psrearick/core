<?php


namespace app\src\form;


use app\src\Model;

class Form
{
    public static function begin($action, $method)
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attr)
    {
        return new InputField($model, $attr);
    }
}