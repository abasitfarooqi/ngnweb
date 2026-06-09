<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('service_bookings'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `service_bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_schedule` tinyint(1) NOT NULL DEFAULT 0,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `reg_no` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
