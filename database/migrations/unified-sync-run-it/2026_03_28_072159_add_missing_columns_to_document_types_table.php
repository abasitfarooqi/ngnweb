<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('document_types', 'slug')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->string('slug', 255)->nullable()->after('name');
            });

            DB::statement('UPDATE `document_types` SET `slug` = `code` WHERE `slug` IS NULL OR `slug` = \'\'');

            DB::statement('ALTER TABLE `document_types` MODIFY `slug` VARCHAR(255) NOT NULL');

            Schema::table('document_types', function (Blueprint $table) {
                $table->unique('slug', 'document_types_slug_unique');
            });
        }

        if (! Schema::hasColumn('document_types', 'is_mandatory')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->boolean('is_mandatory')->default(false)->after('description');
            });

            if (Schema::hasColumn('document_types', 'is_required')) {
                DB::statement('UPDATE `document_types` SET `is_mandatory` = `is_required`');
            }
        }

        if (! Schema::hasColumn('document_types', 'required_for')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->json('required_for')->nullable()->after('is_mandatory');
            });
        }

        if (! Schema::hasColumn('document_types', 'validation_rules')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->json('validation_rules')->nullable()->after('required_for');
            });
        }

        if (! Schema::hasColumn('document_types', 'sort_order')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('validation_rules');
            });

            DB::statement('UPDATE `document_types` SET `sort_order` = `id` WHERE `sort_order` = 0');
        }
    }

    public function down(): void
    {
        if (collect(DB::select("SHOW INDEX FROM `document_types` WHERE Key_name = 'document_types_slug_unique'"))->isNotEmpty()) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->dropUnique('document_types_slug_unique');
            });
        }

        $drop = array_values(array_filter(
            ['sort_order', 'validation_rules', 'required_for', 'is_mandatory', 'slug'],
            fn (string $c) => Schema::hasColumn('document_types', $c)
        ));

        if ($drop !== []) {
            Schema::table('document_types', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }
};
