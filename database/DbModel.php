<?php


namespace core\database;

use core\Application;
use core\Model;

abstract class DbModel extends Model
{

    abstract public function table(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    /**
     * @return bool
     */
    public function save(): bool
    {
        $table = $this->table();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare(
            "INSERT INTO $table (" . implode(',', $attributes) . ") 
            VALUES (" . implode(',', $params) . ")"
        );
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();

        return true;
    }

    /**
     * @param array $cond
     * @return array
     */
    public function findAll(array $cond): array
    {
        $table = static::table();
        $sqlWhere = [];
        $params = [];
        $inParams = [];
        foreach ($cond as $field => $val) {
            if (!is_array($val)){
                if (!property_exists($this, $field)){
                    continue;
                }
                $sqlWhere[] = "$field = :$field";
                $params[$field] = $val;
                continue;
            }
            foreach ($val as $key => $value) {
                if (!property_exists($this, $key)){
                    continue;
                }
                if (array_key_exists('operator', $cond[0]) && $val['operator'] === 'in') {
                    $i = 0;
                    $in = "";
                    foreach ($value as $item) {
                        $k = ":$key".$i++;
                        $in .= "$k,";
                        $inParams[$k] = $item;
                    }
                    $in = rtrim($in,",");
                    $sqlWhere[] = "$key IN ($in)";
                    continue;
                }
                if (array_key_exists('operator', $cond[0]) && $val['operator'] === 'like') {
                    $sqlWhere[] = "$key LIKE :$key";
                    $params[$key] = "%$value%";
                    continue;
                }
                $sqlWhere[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        $params = array_merge($params,$inParams);
        $sql = "SELECT * FROM $table";
        if (!empty($params)) {
            $sql .= " WHERE " . implode(" AND ", $sqlWhere);
        }
        $statement = self::prepare($sql);
        foreach ($params as $param => $val) {
            $statement->bindValue($param, $val);
        }

        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param array $cond
     * @return DbModel
     */
    final public function findOne(array $cond)
    {
        $table = $this->table();
        $attributes  = array_keys($cond);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $table WHERE $sql");
        foreach ($cond as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        $data = $statement->fetchObject();
        if ($data) {
            $this->loadData($data);
        }
        return $this;
    }

    public static function prepare(string $sql): object
    {
        return Application::$app->db->pdo->prepare($sql);
    }

}