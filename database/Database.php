<?php


namespace app\database;


use app\Application;
use PDO;

final class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
        $conf = $config['db'];
        $dsn = $conf['dsn'] ?? '';
        $user = $conf['user'] ?? '';
        $password = $conf['password'] ?? '';
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $applied = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT.'/migrations');
        $toApply = array_diff($files, $applied);
        foreach ($toApply as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT.'/migrations/'.$migration;
            $className = 'app\migrations\\' . pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("$migration applied");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied.");
        }
    }

    public function createMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
                ENGINE=INNODB;");
    }

    public function getAppliedMigrations(): array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare(
            "INSERT INTO migrations (migration) 
                    VALUES $str;
                    ");
        $statement->execute();
    }

    private function log($message)
    {
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }

    public function prepare(string $sql): object
    {
        return $this->pdo->prepare($sql);

    }
}