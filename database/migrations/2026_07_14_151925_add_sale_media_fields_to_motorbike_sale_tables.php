<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes_sale', function (Blueprint $table) {
            if (! Schema::hasColumn('motorbikes_sale', 'video_path')) {
                $table->text('video_path')->nullable();
            }
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            if (! Schema::hasColumn('motorcycles', 'image_two')) {
                $table->text('image_two')->nullable();
            }
            if (! Schema::hasColumn('motorcycles', 'image_three')) {
                $table->text('image_three')->nullable();
            }
            if (! Schema::hasColumn('motorcycles', 'image_four')) {
                $table->text('image_four')->nullable();
            }
            if (! Schema::hasColumn('motorcycles', 'video_path')) {
                $table->text('video_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes_sale', function (Blueprint $table) {
            if (Schema::hasColumn('motorbikes_sale', 'video_path')) {
                $table->dropColumn('video_path');
            }
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            $cols = array_values(array_filter(
                ['image_two', 'image_three', 'image_four', 'video_path'],
                fn (string $col) => Schema::hasColumn('motorcycles', $col)
            ));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
