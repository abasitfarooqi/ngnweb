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
        Schema::table('blog_images', function (Blueprint $table) {
            $table->foreign(['blog_post_id'])->references(['id'])->on('blog_posts')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_images', function (Blueprint $table) {
            $table->dropForeign('blog_images_blog_post_id_foreign');
        });
    }
};
