<?php

namespace App\Console\Commands;

use App\Support\SpareParts\Importers\ConfigCatalogueImporter;
use Illuminate\Console\Command;

class ImportSparePartsCatalogue extends Command
{
    protected $signature = 'spareparts:import-catalogue {--source=config : Import source (config only for now)}';

    protected $description = 'Import spare parts catalogue into relational tables';

    public function handle(ConfigCatalogueImporter $importer): int
    {
        $source = (string) $this->option('source');
        if ($source !== 'config') {
            $this->error("Unsupported source [{$source}]. Currently supported: config");

            return self::FAILURE;
        }

        $catalogue = config('spareparts.catalogue', []);
        if ($catalogue === []) {
            $this->warn('No catalogue data found in config/spareparts.php');

            return self::SUCCESS;
        }

        $this->info('Importing spare parts catalogue from config...');
        $stats = $importer->import($catalogue);

        $this->table(['Entity', 'Imported/Updated'], collect($stats)->map(fn ($count, $entity) => [$entity, $count]));
        $this->info('Spare parts import complete.');

        return self::SUCCESS;
    }
}
