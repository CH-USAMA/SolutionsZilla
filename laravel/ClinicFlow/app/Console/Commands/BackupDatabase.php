<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to local storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        $this->info("Starting backup for connection: {$connection}...");

        $filename = "backup-" . now()->format('Y-m-d-H-i-s');
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        try {
            if ($connection === 'mysql') {
                $this->backupMysql($filename . '.sql', $backupDir);
            } elseif ($connection === 'sqlite') {
                $this->backupSqlite($filename . '.sqlite', $backupDir);
            } else {
                $this->error("Backup for {$connection} is not supported yet.");
                return 1;
            }

            $this->info("Backup completed successfully: {$filename}");

            // Cleanup old backups (keep last 7 days)
            $this->cleanupOldBackups($backupDir);

        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function backupMysql($filename, $backupDir)
    {
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg(config('database.connections.mysql.username')),
            escapeshellarg(config('database.connections.mysql.password')),
            escapeshellarg(config('database.connections.mysql.host')),
            escapeshellarg(config('database.connections.mysql.database')),
            escapeshellarg($backupDir . DIRECTORY_SEPARATOR . $filename)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("mysqldump failed with exit code: {$returnVar}");
        }
    }

    protected function backupSqlite($filename, $backupDir)
    {
        $databasePath = config('database.connections.sqlite.database');

        if ($databasePath === ':memory:') {
            throw new \Exception("Cannot backup in-memory database.");
        }

        if (!File::exists($databasePath)) {
            throw new \Exception("Database file not found at: {$databasePath}");
        }

        File::copy($databasePath, $backupDir . DIRECTORY_SEPARATOR . $filename);
    }

    protected function cleanupOldBackups($backupDir)
    {
        $files = File::files($backupDir);
        $now = time();
        $daysToKeep = 7;

        foreach ($files as $file) {
            if ($now - $file->getMTime() > ($daysToKeep * 24 * 60 * 60)) {
                File::delete($file->getRealPath());
                $this->info("Deleted old backup: " . $file->getFilename());
            }
        }
    }
}
