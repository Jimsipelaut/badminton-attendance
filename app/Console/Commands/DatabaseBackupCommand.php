<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--prune=7 : Days to keep old backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cadangkan basis data MySQL ke file SQL terkompresi lokal dan bersihkan cadangan lama';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');
        $fileName = "backup_{$timestamp}.sql";
        $filePath = "{$backupDir}/{$fileName}";

        $this->info("Memulai pencadangan basis data ke: {$filePath}");

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $tableKey = "Tables_in_{$dbName}";

            $handle = fopen($filePath, 'w');
            fwrite($handle, "-- Backup UKM Bulutangkis Database: {$dbName}\n");
            fwrite($handle, "-- Generated: " . now()->toIso8601String() . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $t) {
                $tableName = $t->$tableKey ?? current((array)$t);

                // DDL Create Table
                $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableSql = $createTableRes[0]->{'Create Table'} ?? current((array)$createTableRes[0]);

                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createTableSql . ";\n\n");

                // Rows
                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    foreach ($rows->chunk(100) as $chunk) {
                        $valuesList = [];
                        foreach ($chunk as $row) {
                            $rowArr = (array)$row;
                            $escapedValues = array_map(function ($val) {
                                if (is_null($val)) return 'NULL';
                                return DB::getPdo()->quote($val);
                            }, $rowArr);
                            $valuesList[] = '(' . implode(', ', $escapedValues) . ')';
                        }
                        $columns = array_map(fn($col) => "`{$col}`", array_keys((array)$chunk->first()));
                        $insertSql = "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES\n" . implode(",\n", $valuesList) . ";\n";
                        fwrite($handle, $insertSql);
                    }
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            $this->info("Pencadangan sukses: " . round(filesize($filePath) / 1024, 2) . " KB");

            // Pembersihan backup lama sesuai retensi (default 7 hari)
            $pruneDays = (int)$this->option('prune');
            $files = File::files($backupDir);
            $deletedCount = 0;
            $cutoff = now()->subDays($pruneDays)->getTimestamp();

            foreach ($files as $file) {
                if ($file->getMTime() < $cutoff && $file->getExtension() === 'sql') {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                $this->info("Menghapus {$deletedCount} file cadangan lama (> {$pruneDays} hari) demi efisiensi kuota.");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Gagal mencadangkan database: " . $e->getMessage());
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            return Command::FAILURE;
        }
    }
}
