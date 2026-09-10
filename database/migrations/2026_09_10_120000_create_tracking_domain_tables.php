<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tracking_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained('motorbikes')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('source_type', 40);
            $table->string('device_identifier')->nullable();
            $table->string('provider')->nullable();
            $table->string('status', 30)->default('inactive');
            $table->boolean('sharing_enabled')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['source_type', 'status']);
            $table->index(['customer_id', 'source_type']);
        });

        Schema::create('tracking_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tracking_source_id')->constrained('tracking_sources')->cascadeOnDelete();
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');
            $table->foreignId('vehicle_id')->nullable()->constrained('motorbikes')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_metres', 10, 2)->nullable();
            $table->decimal('speed', 10, 2)->nullable();
            $table->decimal('heading', 10, 2)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamp('received_at');
            $table->boolean('test_data')->default(false);
            $table->timestamps();
            $table->index('tracking_source_id');
            $table->index(['trackable_type', 'trackable_id']);
            $table->index(['vehicle_id', 'recorded_at']);
            $table->index(['customer_id', 'recorded_at']);
        });

        Schema::create('tracking_alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained('motorbikes')->nullOnDelete();
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');
            $table->foreignId('tracking_source_id')->nullable()->constrained('tracking_sources')->nullOnDelete();
            $table->string('alert_type', 50);
            $table->string('severity', 20)->default('warning');
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['alert_type', 'resolved_at']);
            $table->index(['trackable_type', 'trackable_id']);
        });

        Schema::create('tracking_recovery_modes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('motorbikes')->cascadeOnDelete();
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');
            $table->foreignId('tracking_source_id')->nullable()->constrained('tracking_sources')->nullOnDelete();
            $table->foreignId('activated_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('activated_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->index(['vehicle_id', 'status']);
        });

        Schema::create('location_sharing_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');
            $table->foreignId('tracking_source_id')->nullable()->constrained('tracking_sources')->nullOnDelete();
            $table->string('action', 30);
            $table->string('policy_version')->nullable();
            $table->string('disclosure_version')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['customer_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_sharing_events');
        Schema::dropIfExists('tracking_recovery_modes');
        Schema::dropIfExists('tracking_alerts');
        Schema::dropIfExists('tracking_locations');
        Schema::dropIfExists('tracking_sources');
    }
};
