<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->id();
                $table->string('model_type')->notNullable();
                $table->unsignedBigInteger('model_id')->notNullable();
                $table->char('uuid', 36)->nullable();
                $table->string('collection_name')->notNullable();
                $table->string('name')->notNullable();
                $table->string('file_name')->notNullable();
                $table->string('mime_type')->nullable();
                $table->string('disk')->notNullable();
                $table->string('conversions_disk')->nullable();
                $table->unsignedBigInteger('size')->notNullable();
                $table->longText('manipulations')->charset('utf8mb4')->collation('utf8mb4_bin')->notNullable();
                $table->longText('custom_properties')->charset('utf8mb4')->collation('utf8mb4_bin')->notNullable();
                $table->longText('generated_conversions')->charset('utf8mb4')->collation('utf8mb4_bin')->notNullable();
                $table->longText('responsive_images')->charset('utf8mb4')->collation('utf8mb4_bin')->notNullable();
                $table->integer('order_column')->unsigned()->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique('uuid', 'media_uuid_unique');
                $table->index(['model_type', 'model_id'], 'media_model_type_model_id_index');
                $table->index('order_column', 'media_order_column_index');

                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
