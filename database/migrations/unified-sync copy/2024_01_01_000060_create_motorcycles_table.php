<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema source: production database
        DB::statement('DROP TABLE IF EXISTS '.$this->qid('motorcycles'));
        DB::unprepared(<<<'SQL'
CREATE TABLE `motorcycles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `sale_new_enquire` tinyint(1) DEFAULT NULL,
  `sale_new_price` decimal(8,2) DEFAULT NULL,
  `sale_used_price` decimal(8,2) DEFAULT NULL,
  `rental_deposit` decimal(8,2) DEFAULT NULL,
  `rental_deposit_weeks` int(11) DEFAULT NULL,
  `rental_deposit_paid` tinyint(1) DEFAULT NULL,
  `rental_price` decimal(8,2) DEFAULT NULL,
  `rental_start_date` date DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `npd_test` date DEFAULT NULL,
  `rental_id` bigint(20) DEFAULT NULL,
  `is_insured` tinyint(1) DEFAULT NULL,
  `registration` varchar(255) DEFAULT NULL,
  `registration_place` varchar(255) DEFAULT NULL,
  `registration_date` varchar(255) DEFAULT NULL,
  `make` varchar(70) DEFAULT NULL,
  `model` varchar(70) DEFAULT NULL,
  `year` varchar(70) DEFAULT NULL,
  `colour` varchar(255) DEFAULT NULL,
  `category` varchar(70) DEFAULT NULL,
  `description` varchar(1000) DEFAULT 'Null',
  `road_tax` date DEFAULT NULL,
  `mot` date DEFAULT NULL,
  `insurance` date DEFAULT NULL,
  `vin_number` varchar(255) DEFAULT NULL,
  `engine` varchar(255) DEFAULT NULL,
  `engine_details` varchar(255) DEFAULT NULL,
  `power` varchar(255) DEFAULT NULL,
  `torque` varchar(255) DEFAULT NULL,
  `compression` varchar(255) DEFAULT NULL,
  `bore_x_stroke` varchar(255) DEFAULT NULL,
  `valves_per_cylinder` int(11) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `fuel_system` varchar(255) DEFAULT NULL,
  `fuel_consumption` varchar(255) DEFAULT NULL,
  `lubrication_system` varchar(255) DEFAULT NULL,
  `cooling_system` varchar(255) DEFAULT NULL,
  `gear_box` varchar(255) DEFAULT NULL,
  `clutch` varchar(255) DEFAULT NULL,
  `drive_line` varchar(255) DEFAULT NULL,
  `co2_emissions` varchar(70) DEFAULT NULL,
  `green_house_gases` varchar(255) DEFAULT NULL,
  `emission_details` varchar(255) DEFAULT NULL,
  `exhaust_system` varchar(255) DEFAULT NULL,
  `frame_type` varchar(255) DEFAULT NULL,
  `front_brakes` varchar(255) DEFAULT NULL,
  `front_brakes_diameter` varchar(255) DEFAULT NULL,
  `front_suspension` varchar(255) DEFAULT NULL,
  `front_tyre` varchar(255) DEFAULT NULL,
  `front_wheel_travel` varchar(255) DEFAULT NULL,
  `rake` varchar(255) DEFAULT NULL,
  `rear_brakes` varchar(255) DEFAULT NULL,
  `rear_brakes_diameter` varchar(255) DEFAULT NULL,
  `rear_suspension` varchar(255) DEFAULT NULL,
  `rear_tyre` varchar(255) DEFAULT NULL,
  `rear_wheel_travel` varchar(255) DEFAULT NULL,
  `seat` varchar(255) DEFAULT NULL,
  `trail` varchar(255) DEFAULT NULL,
  `wheel_plan` varchar(255) DEFAULT NULL,
  `alternate_seat_height` varchar(255) DEFAULT NULL,
  `dry_weight` varchar(255) DEFAULT NULL,
  `fuel_capacity` varchar(255) DEFAULT NULL,
  `overall_height` varchar(255) DEFAULT NULL,
  `overall_length` varchar(255) DEFAULT NULL,
  `power_weight_ratio` varchar(255) DEFAULT NULL,
  `reserve_fuel_capacity` varchar(255) DEFAULT NULL,
  `seat_height` varchar(255) DEFAULT NULL,
  `weight_incl_oil_gas_etc` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `starter` varchar(255) DEFAULT NULL,
  `euro_status` varchar(70) DEFAULT NULL,
  `last_v5_issue_date` date DEFAULT NULL,
  `type_approval` varchar(255) DEFAULT NULL,
  `tax_status` varchar(70) DEFAULT NULL,
  `tax_due_date` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `mot_status` varchar(70) DEFAULT NULL,
  `mot_expiry_date` date DEFAULT NULL,
  `month_of_first_registration` varchar(70) DEFAULT NULL,
  `marked_for_export` varchar(70) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `auth_user` varchar(70) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `type` varchar(70) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};
