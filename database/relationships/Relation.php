<?php


namespace app\core\database\relationships;


use core\Application;
use core\Model;

abstract class Relation
{
    abstract public function firstOrNew(int $id): Model;

    abstract public function first(int $id): ?Model;

    abstract public function all(): array;

    abstract public function save(?int $id): void;

    public static function prepare(string $sql): object
    {
        return Application::$app->db->pdo->prepare($sql);
    }

}