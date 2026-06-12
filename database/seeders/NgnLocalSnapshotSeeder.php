<?php

namespace Database\Seeders;

use App\Support\NgnDbSyncToolkit;
use App\Support\ProdToLocalTableSync;
use Illuminate\Database\Seeder;

class NgnLocalSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $connection = (string) config(
            'ngn_local_snapshot.target_connection',
            env('NGN_LOCAL_SNAPSHOT_TARGET_CONNECTION', config('database.default', 'mysql'))
        );
        $configuredFolder = (string) config(
            'ngn_local_snapshot.path',
            env('NGN_LOCAL_SNAPSHOT_PATH', 'database/seeders/data/ngn-local-snapshot')
        );
        $folder = str_starts_with($configuredFolder, '/')
            ? $configuredFolder
            : base_path(trim($configuredFolder, '/'));

        $target = $this->connectionConfig($connection);
        if ($target === null) {
            $this->command?->error("Invalid target connection: {$connection}");

            return;
        }

        try {
            $pdo = ProdToLocalTableSync::connectTarget($target);
            $result = NgnDbSyncToolkit::applySnapshot($pdo, $folder);
        } catch (\Throwable $e) {
            $this->command?->error('Snapshot seed failed: '.$e->getMessage());

            throw $e;
        }

        $this->command?->info('NGN local snapshot applied.');
        $this->command?->line('Tables replayed: '.$result['tables']);
        $this->command?->line('Rows replayed: '.$result['rows']);
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
