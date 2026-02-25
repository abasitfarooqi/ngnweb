<?php

namespace App\Console\Commands;

use App\Services\JudopayEnquiryService;
use Illuminate\Console\Command;

class JudopayEnquiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'judopay:enquiry 
                            {type : Type of enquiry (cit or mit)}
                            {identifier : Reference (for CIT) or Receipt ID (for MIT)}
                            {--reason=manual_command : Reason for the enquiry}
                            {--batch : Process multiple identifiers from stdin}';

    /**
     * The console command description.
     */
    protected $description = 'Make independent JudoPay enquiries for CIT or MIT transactions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $identifier = $this->argument('identifier');
        $reason = $this->option('reason');
        $batch = $this->option('batch');

        // Validate type
        if (! in_array($type, ['cit', 'mit'])) {
            $this->error('Type must be either "cit" or "mit"');

            return 1;
        }

        if ($batch) {
            return $this->handleBatchEnquiry($type, $reason);
        }

        return $this->handleSingleEnquiry($type, $identifier, $reason);
    }

    /**
     * Handle single enquiry
     */
    private function handleSingleEnquiry(string $type, string $identifier, string $reason): int
    {
        $this->info("Making {$type} enquiry for: {$identifier}");
        $this->info("Reason: {$reason}");

        $record = null;

        if ($type === 'cit') {
            $record = JudopayEnquiryService::enquireCitByReference($identifier, $reason);
        } elseif ($type === 'mit') {
            $record = JudopayEnquiryService::enquireMitByReceiptId($identifier, $reason);
        }

        if ($record) {
            $this->info("✅ Enquiry successful - Record ID: {$record->id}");

            // Display results in a table
            $this->table(
                ['Field', 'Value'],
                [
                    ['Enquiry ID', $record->id],
                    ['Type', $record->transaction_type],
                    ['Identifier', $record->enquiry_identifier],
                    ['API Status', $record->api_status],
                    ['HTTP Code', $record->http_status_code],
                    ['JudoPay Status', $record->judopay_status ?? 'N/A'],
                    ['Bank Code', $record->external_bank_response_code ?? 'N/A'],
                    ['Amount Collected', '£'.($record->amount_collected_remote ?? '0.00')],
                    ['Message', $record->remote_message ?? 'N/A'],
                    ['Is Retryable', $record->is_retryable ? 'Yes' : 'No'],
                    ['Enquired At', $record->enquired_at->format('Y-m-d H:i:s')],
                ]
            );

            return 0;
        } else {
            $this->error('❌ Enquiry failed');

            return 1;
        }
    }

    /**
     * Handle batch enquiry from stdin or file
     */
    private function handleBatchEnquiry(string $type, string $reason): int
    {
        $this->info("Batch {$type} enquiry mode");
        $this->info('Enter identifiers (one per line), press Ctrl+D when done:');

        $identifiers = [];

        // Read from stdin
        while (($line = fgets(STDIN)) !== false) {
            $identifier = trim($line);
            if (! empty($identifier)) {
                $identifiers[] = $identifier;
            }
        }

        if (empty($identifiers)) {
            $this->error('No identifiers provided');

            return 1;
        }

        $this->info('Processing '.count($identifiers).' identifiers...');

        $enquiries = array_map(function ($identifier) use ($type) {
            return [
                'type' => $type,
                'identifier' => $identifier,
            ];
        }, $identifiers);

        $results = JudopayEnquiryService::enquireBatch($enquiries, $reason);

        $this->info('✅ Batch enquiry complete - '.count($results).' records created');

        // Display summary table
        if (! empty($results)) {
            $tableData = [];
            foreach ($results as $record) {
                $tableData[] = [
                    $record->id,
                    $record->transaction_type,
                    $record->enquiry_identifier,
                    $record->judopay_status ?? 'Failed',
                    $record->external_bank_response_code ?? 'N/A',
                    '£'.($record->amount_collected_remote ?? '0.00'),
                    $record->is_retryable ? 'Yes' : 'No',
                ];
            }

            $this->table(
                ['ID', 'Type', 'Identifier', 'Status', 'Bank Code', 'Amount', 'Retryable'],
                $tableData
            );
        }

        return 0;
    }
}
