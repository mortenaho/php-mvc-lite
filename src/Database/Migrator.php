<?php

declare(strict_types=1);

namespace Lite\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use RuntimeException;

final class Migrator
{
    public function __construct(
        private readonly string $path,
        private readonly Capsule $capsule,
    ) {
    }

    public function migrate(): array
    {
        $this->ensureMigrationsTable();
        $ran = $this->ran();
        $executed = [];

        foreach ($this->files() as $file) {
            $name = basename($file, '.php');

            if (in_array($name, $ran, true)) {
                continue;
            }

            $migration = require $file;

            if (! is_object($migration) || ! method_exists($migration, 'up')) {
                throw new RuntimeException("Migration [{$name}] must return an object with an up() method.");
            }

            $migration->up($this->capsule);
            $this->capsule->table('migrations')->insert([
                'migration' => $name,
                'batch' => $this->nextBatch(),
            ]);
            $executed[] = $name;
        }

        return $executed;
    }

    public function rollback(): array
    {
        $this->ensureMigrationsTable();
        $batch = (int) $this->capsule->table('migrations')->max('batch');

        if ($batch === 0) {
            return [];
        }

        $rows = $this->capsule->table('migrations')
            ->where('batch', $batch)
            ->orderByDesc('id')
            ->get();

        $rolled = [];

        foreach ($rows as $row) {
            $file = $this->path . DIRECTORY_SEPARATOR . $row->migration . '.php';

            if (! is_file($file)) {
                continue;
            }

            $migration = require $file;

            if (is_object($migration) && method_exists($migration, 'down')) {
                $migration->down($this->capsule);
            }

            $this->capsule->table('migrations')->where('migration', $row->migration)->delete();
            $rolled[] = $row->migration;
        }

        return $rolled;
    }

    public function fresh(): array
    {
        $this->dropAllTables();

        return $this->migrate();
    }

    private function dropAllTables(): void
    {
        $schema = $this->capsule->schema();
        $schema->disableForeignKeyConstraints();

        foreach ($schema->getTableListing() as $table) {
            if (in_array($table, ['sqlite_sequence'], true)) {
                continue;
            }

            $schema->drop($table);
        }

        $schema->enableForeignKeyConstraints();
    }

    private function ensureMigrationsTable(): void
    {
        if ($this->capsule->schema()->hasTable('migrations')) {
            return;
        }

        $this->capsule->schema()->create('migrations', function (Blueprint $table): void {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
    }

    /**
     * @return list<string>
     */
    private function ran(): array
    {
        return $this->capsule->table('migrations')->pluck('migration')->all();
    }

    private function nextBatch(): int
    {
        return ((int) $this->capsule->table('migrations')->max('batch')) + 1;
    }

    /**
     * @return list<string>
     */
    private function files(): array
    {
        $files = glob($this->path . '/*.php') ?: [];
        sort($files);

        return $files;
    }
}
