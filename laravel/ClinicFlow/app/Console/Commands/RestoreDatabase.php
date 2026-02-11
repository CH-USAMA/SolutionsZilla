<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {filename? : The backup file to restore}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database from a local backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            $this->error("Backup directory not found.");
            return 1;
        }

        $filename = $this->argument('filename');

        if (!$filename) {
            $files = File::files($backupDir);
            if (empty($files)) {
                $this->error("No backups found.");
                return 1;
            }

            // Get most recent backup
            usort($files, function ($a, $b) {
                return $b->getMTime() <=> $a->getMTime();
            });

            $filename = $files[0]->getFilename();
            if (!$this->confirm("No filename provided. Use most recent: {$filename}?", true)) {
                return 0;
            }
        }

        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($backupPath)) {
            $this->error("Backup file not found: {$filename}");
            return 1;
        }

        $this->warn("CAUTION: This will overwrite your current database!");
        if (!$this->confirm("Are you absolutely sure you want to proceed?", false)) {
            return 0;
        }

        $connection = config('database.default');

        try {
            if ($connection === 'mysql') {
                $this->restoreMysql($backupPath);
            } elseif ($connection === 'sqlite') {
                $this->restoreSqlite($backupPath);
            } else {
                $this->error("Restore for {$connection} is not supported yet.");
                return 1;
            }

            $this->info("Database restored successfully from: {$filename}");

        } catch (\Exception $e) {
            $this->error("Restore failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function restoreMysql($backupPath)
    {
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            escapeshellarg(config('database.connections.mysql.username')),
            escapeshellarg(config('database.connections.mysql.password')),
            escapeshellarg(config('database.connections.mysql.host')),
            escapeshellarg(config('database.connections.mysql.database')),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("mysql restore failed with exit code: {$returnVar}");
        }
    }

    protected function restoreSqlite($backupPath)
    {
        $databasePath = config('database.connections.sqlite.database');

        if ($databasePath === ':memory:') {
            throw new \Exception("Cannot restore into in-memory database.");
        }

        // Drop connection or clear cache might be needed but for SQLite simple copy works
        File::copy($backupPath, $databasePath);
    }
}
