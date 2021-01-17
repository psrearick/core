<?php


namespace app;


use app\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}