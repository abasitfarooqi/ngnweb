<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Safe column/index/FK alignment: production + local merged (idempotent). */
return new class extends Migration
{
    public function up(): void
    {
        $this->alignBranches();
        $this->alignClubMembersAndCustomers();
        $this->alignDocumentTypes();
        $this->alignServiceBookings();
        $this->alignEcOrderItems();
        $this->alignPayments();
        $this->alignFinanceApplications();
        $this->alignServiceBookingsProductionWorkflow();
    }

    private function alignBranches(): void
    {
        if (Schema::hasTable('branches') && ! Schema::hasColumn('branches', 'opening_hours')) {
            DB::statement('ALTER TABLE `branches` ADD COLUMN `opening_hours` text COLLATE utf8mb4_unicode_ci NULL AFTER `city`');
        }
    }

    private function alignClubMembersAndCustomers(): void
    {
        if (Schema::hasTable('club_members') && ! Schema::hasColumn('club_members', 'customer_id')) {
            DB::statement('ALTER TABLE `club_members` ADD COLUMN `customer_id` bigint unsigned NULL DEFAULT NULL AFTER `user_id`');
        }

        if (Schema::hasTable('club_members') && Schema::hasColumn('club_members', 'customer_id')) {
            if (! $this->hasIndex('club_members', 'club_members_customer_id_index')) {
                DB::statement('ALTER TABLE `club_members` ADD INDEX `club_members_customer_id_index` (`customer_id`)');
            }
            if (! $this->hasForeignKey('club_members', 'club_members_customer_id_foreign') && Schema::hasTable('customers')) {
                DB::statement('ALTER TABLE `club_members` ADD CONSTRAINT `club_members_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL');
            }
        }

        if (Schema::hasTable('customers') && ! Schema::hasColumn('customers', 'is_club')) {
            DB::statement('ALTER TABLE `customers` ADD COLUMN `is_club` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_register`');
        }
    }

    private function alignDocumentTypes(): void
    {
        if (! Schema::hasTable('document_types')) {
            return;
        }

        if (! Schema::hasColumn('document_types', 'slug')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->string('slug', 255)->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('document_types', 'is_mandatory')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->boolean('is_mandatory')->default(false)->after('description');
            });
            if (Schema::hasColumn('document_types', 'is_required')) {
                DB::statement('UPDATE `document_types` SET `is_mandatory` = `is_required`');
            }
        }

        if (! Schema::hasColumn('document_types', 'required_for')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->json('required_for')->nullable()->after('is_mandatory');
            });
        }

        if (! Schema::hasColumn('document_types', 'validation_rules')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->json('validation_rules')->nullable()->after('required_for');
            });
        }

        if (! Schema::hasColumn('document_types', 'sort_order')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->integer('sort_order')->default(0)->after('validation_rules');
            });
            DB::statement('UPDATE `document_types` SET `sort_order` = `id` WHERE `sort_order` = 0');
        }

        if (Schema::hasColumn('document_types', 'code')) {
            DB::statement("UPDATE `document_types` SET `slug` = `code` WHERE `slug` IS NULL OR `slug` = ''");
        } else {
            DB::statement("UPDATE `document_types` SET `slug` = CONCAT('type-', `id`) WHERE `slug` IS NULL OR `slug` = ''");
        }

        foreach (DB::select('SELECT `slug`, MIN(`id`) AS keep_id, COUNT(*) AS total FROM `document_types` GROUP BY `slug` HAVING COUNT(*) > 1') as $duplicate) {
            DB::statement(
                'UPDATE `document_types` SET `slug` = CONCAT(`slug`, \'-\', `id`) WHERE `slug` = ? AND `id` <> ?',
                [$duplicate->slug, $duplicate->keep_id]
            );
        }

        DB::statement('ALTER TABLE `document_types` MODIFY `slug` varchar(255) NOT NULL');

        if (! $this->hasIndex('document_types', 'document_types_slug_unique')) {
            Schema::table('document_types', function (Blueprint $table): void {
                $table->unique('slug', 'document_types_slug_unique');
            });
        }
    }

    private function alignServiceBookings(): void
    {
        if (! Schema::hasTable('service_bookings')) {
            return;
        }

        $columns = [
            'customer_id' => 'bigint unsigned NULL DEFAULT NULL AFTER `id`',
            'customer_auth_id' => 'bigint unsigned NULL DEFAULT NULL AFTER `customer_id`',
            'submission_context' => 'varchar(40) NULL DEFAULT NULL AFTER `customer_auth_id`',
            'enquiry_type' => 'varchar(80) NULL DEFAULT NULL AFTER `submission_context`',
            'subject' => 'varchar(255) NULL DEFAULT NULL AFTER `service_type`',
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('service_bookings', $column)) {
                DB::statement("ALTER TABLE `service_bookings` ADD COLUMN `{$column}` {$definition}");
            }
        }

        foreach ([
            'customer_id' => 'service_bookings_customer_id_index',
            'customer_auth_id' => 'service_bookings_customer_auth_id_index',
            'enquiry_type' => 'service_bookings_enquiry_type_index',
            'submission_context' => 'service_bookings_submission_context_index',
        ] as $column => $indexName) {
            if (Schema::hasColumn('service_bookings', $column) && ! $this->hasIndex('service_bookings', $indexName)) {
                DB::statement("ALTER TABLE `service_bookings` ADD INDEX `{$indexName}` (`{$column}`)");
            }
        }
    }

    private function alignServiceBookingsProductionWorkflow(): void
    {
        if (! Schema::hasTable('service_bookings')) {
            return;
        }

        if (! Schema::hasColumn('service_bookings', 'conversation_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `conversation_id` bigint unsigned NULL DEFAULT NULL');
        }
        if (! Schema::hasColumn('service_bookings', 'is_dealt')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `is_dealt` tinyint(1) NOT NULL DEFAULT 0');
        }
        if (! Schema::hasColumn('service_bookings', 'dealt_by_user_id')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `dealt_by_user_id` bigint unsigned NULL DEFAULT NULL');
        }
        if (! Schema::hasColumn('service_bookings', 'notes')) {
            DB::statement('ALTER TABLE `service_bookings` ADD COLUMN `notes` text COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');
        }

        if (Schema::hasTable('support_conversations') && Schema::hasColumn('service_bookings', 'conversation_id')) {
            if (! $this->hasForeignKey('service_bookings', 'service_bookings_conversation_id_foreign')) {
                DB::statement('ALTER TABLE `service_bookings` ADD CONSTRAINT `service_bookings_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `support_conversations` (`id`) ON DELETE SET NULL');
            }
            if (! $this->hasIndex('service_bookings', 'service_bookings_conversation_id_index')) {
                DB::statement('ALTER TABLE `service_bookings` ADD INDEX `service_bookings_conversation_id_index` (`conversation_id`)');
            }
        }
    }

    private function alignEcOrderItems(): void
    {
        if (! Schema::hasTable('ec_order_items')) {
            return;
        }

        if (! Schema::hasColumn('ec_order_items', 'item_type')) {
            DB::statement("ALTER TABLE `ec_order_items` ADD COLUMN `item_type` varchar(255) NOT NULL DEFAULT 'catalogue' AFTER `product_id`");
        }
        if (! Schema::hasColumn('ec_order_items', 'part_number')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `part_number` varchar(255) NULL DEFAULT NULL AFTER `sku`');
        }
        if (! Schema::hasColumn('ec_order_items', 'sp_part_id')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_part_id` bigint unsigned NULL DEFAULT NULL AFTER `part_number`');
        }
        if (! Schema::hasColumn('ec_order_items', 'sp_assembly_id')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `sp_assembly_id` bigint unsigned NULL DEFAULT NULL AFTER `sp_part_id`');
        }
        if (! Schema::hasColumn('ec_order_items', 'source_meta')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD COLUMN `source_meta` json NULL DEFAULT NULL AFTER `line_total`');
        }

        foreach (['item_type', 'part_number', 'sp_part_id', 'sp_assembly_id'] as $column) {
            $indexName = "ec_order_items_{$column}_index";
            if (Schema::hasColumn('ec_order_items', $column) && ! $this->hasIndex('ec_order_items', $indexName)) {
                DB::statement("ALTER TABLE `ec_order_items` ADD INDEX `{$indexName}` (`{$column}`)");
            }
        }

        if (! Schema::hasColumn('ec_order_items', 'product_id')) {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM `ec_order_items` WHERE Field = 'product_id'"))->first();
        if ($column && strtoupper((string) $column->Null) === 'YES') {
            return;
        }

        if ($this->hasForeignKey('ec_order_items', 'ec_order_items_product_id_foreign')) {
            DB::statement('ALTER TABLE `ec_order_items` DROP FOREIGN KEY `ec_order_items_product_id_foreign`');
        }

        DB::statement('ALTER TABLE `ec_order_items` MODIFY `product_id` bigint unsigned NULL');

        if (Schema::hasTable('ngn_products')) {
            DB::statement('ALTER TABLE `ec_order_items` ADD CONSTRAINT `ec_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ngn_products` (`id`) ON DELETE SET NULL');
        }
    }

    private function alignPayments(): void
    {
        if (! Schema::hasTable('payments') || Schema::hasColumn('payments', 'pcn_case_id')) {
            return;
        }

        DB::statement('ALTER TABLE `payments` ADD COLUMN `pcn_case_id` bigint unsigned NULL DEFAULT NULL AFTER `user_id`');
        if (! $this->hasIndex('payments', 'payments_pcn_case_id_index')) {
            DB::statement('ALTER TABLE `payments` ADD INDEX `payments_pcn_case_id_index` (`pcn_case_id`)');
        }
    }

    private function alignFinanceApplications(): void
    {
        if (Schema::hasTable('finance_applications') && ! Schema::hasColumn('finance_applications', 'is_new')) {
            Schema::table('finance_applications', function (Blueprint $table): void {
                $table->boolean('is_new')->default(false)->after('sold_by');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$indexName]))->isNotEmpty();
    }

    private function hasForeignKey(string $table, string $constraintName): bool
    {
        return collect(DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\'',
            [$table, $constraintName]
        ))->isNotEmpty();
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
