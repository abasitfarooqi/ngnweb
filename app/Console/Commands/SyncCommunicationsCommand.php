<?php

namespace App\Console\Commands;

use App\Services\Communications\CommunicationDefinitionSynchronizer;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Console\Command;

class SyncCommunicationsCommand extends Command
{
    protected $signature = 'communications:sync';

    protected $description = 'Synchronize code-defined transactional communication metadata without overwriting staff delivery policy';

    public function handle(CommunicationDefinitionSynchronizer $synchronizer, CommunicationSchema $schema): int
    {
        if (! $schema->ready()) {
            $this->warn('Communication definitions were not synchronized because the communication tables are missing.');
            $this->line('Missing tables: '.implode(', ', $schema->missingTables()));
            $this->line('Legacy transactional email behaviour is unchanged.');

            return self::SUCCESS;
        }

        $result = $synchronizer->sync();

        $this->info(sprintf(
            'Communication definitions synchronized. Created: %d, updated: %d, skipped: %d.',
            $result['created'],
            $result['updated'],
            $result['skipped'],
        ));

        return self::SUCCESS;
    }
}
