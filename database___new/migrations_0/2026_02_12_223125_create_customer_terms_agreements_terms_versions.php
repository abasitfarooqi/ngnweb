<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_terms_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('terms_version_id')->constrained('terms_versions')->onDelete('restrict');
            $table->timestamp('agreed_at');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::table('customer_auths', function (Blueprint $table) {
            $table->foreignId('current_terms_version_id')->nullable()
                ->constrained('terms_versions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('customer_auths', function (Blueprint $table) {
            $table->dropForeign(['current_terms_version_id']);
            $table->dropColumn('current_terms_version_id');
        });
        Schema::dropIfExists('customer_terms_agreements');
        Schema::dropIfExists('terms_versions');
    }
};
