<?php


namespace app\form;


use app\Model;

final class Form
{
    public static function begin(string $action, string $method): Form
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end(): bool
    {
        echo '</form>';
        return true;
    }

    public function field(Model $model, string $attr): InputField
    {
        return new InputField($model, $attr);
    }
}