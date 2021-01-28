<?php


namespace app\core\database\relationships;


use core\database\DbModel;
use core\Model;

class BelongsToMany extends Relation
{
    protected string $table;

    protected string $foreignKey;

    protected string $relatedKey;

    protected array $columns;

    protected DbModel $parent;

    protected DbModel $related;

    public function __construct(DbModel $parent, DbModel $related, string $table, string $relatedKey, string $foreignKey, array $columns = [])
    {
        $this->table = $table;
        $this->parent = $parent;
        $this->related = $related;
        $this->relatedKey = $relatedKey;
        $this->foreignKey = $foreignKey;
        $this->columns = $columns;
    }

    public function firstOrNew(int $id): Model
    {
        $relatedModel = $this->related->newInstance();
        $statement = $this->query("1");
        $data = $statement->fetchObject();
        if ($data) {
            $relatedModel->loadData($data);
        }
        return $relatedModel;
    }

    public function first(int $id): ?Model
    {
        $statement = $this->query("1");
        $data = $statement->fetchObject();
        if ($data) {
            $relatedModel = $this->related->newInstance();
            $relatedModel->loadData($data);
        }
        return null;
    }

    private function query(string $select = "*"): object
    {
        $id = $this->parent->getId();
        $statement = self::prepare(
            "SELECT $select FROM $this->table WHERE $this->relatedKey = $id"
        );
        $statement->execute();
        return $statement;
    }

    public function all(): array
    {
        $records = [];
        $statement = $this->query();
        $data = $statement->fetchObject();
        foreach ($data as $record) {
            $first = $this->first($record[$this->foreignKey]);
            if ($first) {
                $records[] = $first;
            }
        }
        return $records;
    }

    public function save(?int $id): void
    {
        $relatedId = $this->related->getId();
        if ($id && !$relatedId) {
            $this->related = $this->related->findOne([$this->related->primaryKey() => $id]);
            $relatedId = $this->related->getId();
        }
        if (!$relatedId) {
            return;
        }
        if ($this->first($relatedId)) {
            return;
        }
        $columns = $this->columns;
        if (empty($columns)) {
            $columns = [$this->relatedKey, $this->foreignKey];
        }
        $params = array_map(fn($attr) => ":$attr", $columns);
        $statement = self::prepare(
            "INSERT INTO $this->table (" . implode(',', $columns) . ")
                VALUES (" . implode(',', $params) . ")"
        );
        foreach ($columns as $attribute) {
            if ($attribute === $this->foreignKey) {
                $statement->bindValue(":$attribute", $this->related->id);
                continue;
            }
            if ($attribute === $this->relatedKey) {
                $statement->bindValue(":$attribute", $this->parent->id);
                continue;
            }
            $statement->bindValue(":$attribute", $this->parent->{$attribute});
        }
        $statement->execute();
    }
}