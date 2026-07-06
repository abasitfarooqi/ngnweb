<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ngn_products')) {
            return;
        }

        Schema::table('ngn_products', function (Blueprint $table) {
            if (! Schema::hasColumn('ngn_products', 'parent_product_id')) {
                $table->unsignedBigInteger('parent_product_id')->nullable()->after('id');
                $table->index('parent_product_id', 'ngn_products_parent_product_id_index');
            }

            if (! Schema::hasColumn('ngn_products', 'video_url')) {
                $table->string('video_url', 1024)->nullable()->after('image_url');
            }

            if (! Schema::hasColumn('ngn_products', 'size_label')) {
                $table->string('size_label', 32)->nullable()->after('colour');
            }
        });

        if (
            Schema::hasColumn('ngn_products', 'parent_product_id')
            && ! $this->foreignKeyExists('ngn_products', 'ngn_products_parent_product_id_foreign')
        ) {
            Schema::table('ngn_products', function (Blueprint $table) {
                $table->foreign('parent_product_id', 'ngn_products_parent_product_id_foreign')
                    ->references('id')
                    ->on('ngn_products')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }

    private function foreignKeyExists(string $table, string $name): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $row = $connection->selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$database, $table, $name, 'FOREIGN KEY']
        );

        return $row !== null;
    }
};
