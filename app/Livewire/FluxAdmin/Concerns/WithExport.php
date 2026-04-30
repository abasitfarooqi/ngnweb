<?php

namespace App\Livewire\FluxAdmin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a CSV export for the current filtered query.
 *
 * The page should expose:
 *  - exportQuery(): Builder          The filtered builder (no pagination).
 *  - exportColumns(): array          [ 'header' => callable|string (accessor / field name) ]
 *  - exportFilename(): ?string       Optional filename without extension.
 */
trait WithExport
{
    public function exportCsv(): StreamedResponse
    {
        $filename = ($this->exportFilename ?? 'export-'.Str::slug(static::class))
            .'-'.Carbon::now()->format('Ymd-His').'.csv';

        /** @var Builder $query */
        $query = $this->exportQuery();
        $columns = $this->exportColumns();

        return response()->streamDownload(function () use ($query, $columns): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, array_keys($columns));

            $query->chunk(500, function ($rows) use ($handle, $columns): void {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($columns as $accessor) {
                        if (is_callable($accessor)) {
                            $line[] = (string) $accessor($row);

                            continue;
                        }
                        $line[] = (string) data_get($row, $accessor, '');
                    }
                    fputcsv($handle, $line);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    abstract protected function exportQuery(): Builder;

    /**
     * @return array<string, callable|string>
     */
    abstract protected function exportColumns(): array;

    protected string $exportFilename = '';
}
