<?php

namespace Database\Seeders;

use App\Support\ProdToLocalTableSync;
use Illuminate\Database\Seeder;

class NgnCleanOnlySnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $connection = (string) env('NGN_CLONE_TARGET_CONNECTION', config('database.default', 'mysql'));
        $target = $this->connectionConfig($connection);
        if ($target === null) {
            $this->command?->error("Invalid target connection: {$connection}");

            return;
        }

        $folder = base_path(trim((string) env('NGN_CLEAN_SNAPSHOT_PATH', 'database/seeders/data/ngn-clean-only'), '/'));
        if (! is_dir($folder)) {
            $this->command?->error("Snapshot folder not found: {$folder}");

            return;
        }

        $manifestPath = $folder.'/manifest.json';
        if (! is_file($manifestPath)) {
            $this->command?->error("manifest.json not found in snapshot folder: {$folder}");

            return;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $tables = is_array($manifest['tables'] ?? null) ? $manifest['tables'] : [];
        if ($tables === []) {
            $this->command?->warn('No tables listed in manifest.');

            return;
        }

        try {
            $dst = ProdToLocalTableSync::connectTarget($target);
        } catch (\Throwable $e) {
            $this->command?->error('Target DB connection failed: '.$e->getMessage());

            return;
        }

        $this->command?->info('Applying ngn_clean-only snapshot to '.$target['database'].'...');

        foreach ($tables as $meta) {
            $table = (string) ($meta['name'] ?? '');
            if ($table === '') {
                continue;
            }

            $file = $folder.'/'.$table.'.json';
            if (! is_file($file)) {
                $this->command?->warn("  {$table}: snapshot file missing, skipped");

                continue;
            }

            $payload = json_decode((string) file_get_contents($file), true);
            $columns = is_array($payload['columns'] ?? null) ? $payload['columns'] : [];
            $rows = is_array($payload['rows'] ?? null) ? $payload['rows'] : [];

            try {
                $dst->exec('SET FOREIGN_KEY_CHECKS=0');
                $dst->exec('SET UNIQUE_CHECKS=0');
                $dst->exec('TRUNCATE TABLE '.ProdToLocalTableSync::qid($table));

                if ($rows !== [] && $columns !== []) {
                    $colList = implode(',', array_map([ProdToLocalTableSync::class, 'qid'], $columns));
                    $placeholders = implode(',', array_fill(0, count($columns), '?'));
                    $insertSql = 'INSERT INTO '.ProdToLocalTableSync::qid($table).' ('.$colList.') VALUES ('.$placeholders.')';
                    $insert = $dst->prepare($insertSql);

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($columns as $column) {
                            $values[] = $row[$column] ?? null;
                        }
                        $insert->execute($values);
                    }
                }
                $dst->exec('SET UNIQUE_CHECKS=1');
                $dst->exec('SET FOREIGN_KEY_CHECKS=1');
                $this->command?->line("  {$table}: ".count($rows).' rows');
            } catch (\Throwable $e) {
                $dst->exec('SET UNIQUE_CHECKS=1');
                $dst->exec('SET FOREIGN_KEY_CHECKS=1');
                $this->command?->warn("  {$table}: failed - ".$e->getMessage());
            }
        }

        $this->command?->info('Snapshot seeding finished.');
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function connectionConfig(string $connection): ?array
    {
        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            return null;
        }

        $host = (string) ($cfg['host'] ?? '');
        $database = (string) ($cfg['database'] ?? '');
        $username = (string) ($cfg['username'] ?? '');
        $password = (string) ($cfg['password'] ?? '');

        if ($host === '' || $database === '' || $username === '') {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ];
    }
}

