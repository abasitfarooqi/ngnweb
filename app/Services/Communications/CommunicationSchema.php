<?php

namespace App\Services\Communications;

use Illuminate\Support\Facades\Schema;

class CommunicationSchema
{
    /**
     * @return list<string>
     */
    public function requiredTables(): array
    {
        return [
            'communication_definitions',
            'communication_policies',
            'communications',
            'communication_recipients',
            'communication_deliveries',
            'communication_attachments',
            'communication_audits',
        ];
    }

    public function ready(): bool
    {
        foreach ($this->requiredTables() as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    public function missingTables(): array
    {
        return array_values(array_filter(
            $this->requiredTables(),
            fn (string $table): bool => ! Schema::hasTable($table)
        ));
    }

    public function repliesReady(): bool
    {
        return $this->ready() && Schema::hasTable('communication_replies');
    }
}
