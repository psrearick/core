<?php


namespace core;


use core\database\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}