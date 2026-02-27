<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorbikeRepairServicesListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: motorbike_repair_services_lists
     * Records: 42
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `motorbike_repair_services_lists`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `motorbike_repair_services_lists` (`id`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES
('1', 'Engine Oil Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('2', 'Oil Filter Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('3', 'Air Filter Cleaning & Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('4', 'Spark Plug Inspection & Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('5', 'Chain Cleaning, lubricating, and adjusting the chain', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('6', 'Belt inspection for wear', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('7', 'Gearbox Oil Inspection & Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('8', 'Clutch Adjustment', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('9', 'Brake Check Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('10', 'Brake Fluid Check & Top-up', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('11', 'Brake Operation Test', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('12', 'Brake Pads/Discs Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('13', 'Brake Fluid Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('14', 'Brake Calipers Inspection & Cleaning', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('15', 'Tire Pressure Check', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('16', 'Tire Inspection for Tread Wear or Damage', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('17', 'Wheel Bearings Check', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('18', 'Chain Lubrication', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('19', 'Chain Check & Adjustment for Proper Operation', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('20', 'Drive Belt Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('21', 'Drive Belt Wear Check', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('22', 'Fork Seal Check for Leakage', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('23', 'Steering Head Bearings Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('24', 'Shock Absorbers Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('25', 'Lights Checking & Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('26', 'Battery Check', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('27', 'Horn Test', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('28', 'Wiring Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('29', 'Checking Coolant Level', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('30', 'Clutch & Throttle Cable Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('31', 'Coolant Replacement', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('32', 'Checking & Tightening Loose Bolts & Nuts', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('33', 'Frame Inspection', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('34', 'Inspecting the coolant level and replacing if needed.', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('35', 'Radiator checking for blockages, leaks, or damage.', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('36', 'Inspecting for Leaks, Rust, or Damage', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('37', 'Checking All Mountings & Brackets', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('38', 'Checking and tightening loose bolts and nuts.', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('39', 'Inspecting for leaks, rust, or visible damage.', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('40', 'Throttle and Clutch Cable Adjustment.', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('41', 'Test Ride', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19'),
('42', 'Software Updates', NULL, NULL, '2025-09-02 20:13:19', '2025-09-02 20:13:19');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
