<?php

namespace Database\Seeders\ProductionData;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generated from production data.
     * Table: media
     * Records: 13
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE `media`');
        
        DB::unprepared(<<<'SQL'
INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
('2', 'brand', '2', 'f6c87048-8b2d-4d8d-a153-bba710bf7e7e', 'uploads', 'DQxwB8GYJ7Mfy0RB6KaF53oQHRIW85-metaeWFtYWhhLW1vdG9yY3ljbGVzLXVrbC1sb2dvLnBuZw==-', 'DQxwB8GYJ7Mfy0RB6KaF53oQHRIW85-metaeWFtYWhhLW1vdG9yY3ljbGVzLXVrbC1sb2dvLnBuZw==-.png', 'image/png', 'public', 'public', '93683', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-11 03:55:28', '2023-04-11 03:55:28'),
('3', 'brand', '1', '6b6c3937-f7ea-4f9e-8c47-7be76454f66a', 'uploads', 'C2tkWkeIxAf7nhf8OCUTN7AE3gMskr-metaaG9uZGEtbG9nby5wbmc=-', 'C2tkWkeIxAf7nhf8OCUTN7AE3gMskr-metaaG9uZGEtbG9nby5wbmc=-.png', 'image/png', 'public', 'public', '15474', '[]', '[]', '{\"thumb200x200\": true}', '[]', '2', '2023-04-11 03:56:07', '2023-04-11 03:56:07'),
('4', 'brand', '3', 'eee65ab0-6900-4ffc-bc8f-559376649d60', 'uploads', 'ZDEiQdHay2hJvnv2vAyVqnHVgxc8YA-metaYm94LWhlbG1ldHMtdmVjdG9yLWxvZ28ucG5n-', 'ZDEiQdHay2hJvnv2vAyVqnHVgxc8YA-metaYm94LWhlbG1ldHMtdmVjdG9yLWxvZ28ucG5n-.png', 'image/png', 'public', 'public', '6373', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-15 09:36:27', '2023-04-15 09:36:27'),
('5', 'category', '75', '90bd73b7-23f4-4ffc-9381-77ffbdf6f793', 'uploads', '51jf7nES5OoYWvDPHbf0v1O8zSexE3-metaZm9yLXNhbGUuanBn-', '51jf7nES5OoYWvDPHbf0v1O8zSexE3-metaZm9yLXNhbGUuanBn-.jpg', 'image/jpeg', 'public', 'public', '251781', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-18 15:43:27', '2023-04-18 15:43:27'),
('6', 'category', '76', '2fe9a99b-f397-4360-a25c-a9b74ae56951', 'uploads', '0gOKHRP0sYnSmKSIjO9X74OoR3yI23-metac3RyZWV0LnBuZw==-', '0gOKHRP0sYnSmKSIjO9X74OoR3yI23-metac3RyZWV0LnBuZw==-.png', 'image/png', 'public', 'public', '313187', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-18 15:47:51', '2023-04-18 15:47:51'),
('7', 'product', '1', 'd6fd0611-f5ee-470c-805d-4ec037ceb530', 'uploads', 'iMR7Lt80uS7tG2dSjS31zhBwo2dY5B-metaY2I3NTAtaG9ybmV0LTIuanBn-', 'iMR7Lt80uS7tG2dSjS31zhBwo2dY5B-metaY2I3NTAtaG9ybmV0LTIuanBn-.jpg', 'image/jpeg', 'public', 'public', '171814', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-18 15:56:22', '2023-04-18 15:56:22'),
('8', 'product', '2', 'e6b3a81b-5261-4660-a856-1b7dee0a2d8a', 'uploads', 'to6fBJO576yBXkNIBOncRwcxgSV01v-metaY2I3NTAuanBn-', 'to6fBJO576yBXkNIBOncRwcxgSV01v-metaY2I3NTAuanBn-.jpg', 'image/jpeg', 'public', 'public', '24787', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-22 15:33:37', '2023-04-22 15:33:38'),
('9', 'product', '3', '1ad18e15-d3a5-48ef-8338-53d75460c2d3', 'uploads', 'dD4CsB6CRiNWKRhJ8UYemab1LQorXX-metaeWFtYWhhLXNlY29uZC1oYW5kLmpwZw==-', 'dD4CsB6CRiNWKRhJ8UYemab1LQorXX-metaeWFtYWhhLXNlY29uZC1oYW5kLmpwZw==-.jpg', 'image/jpeg', 'public', 'public', '115469', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-24 13:51:37', '2023-04-24 13:51:37'),
('10', 'product', '4', '271a1275-bb06-4bce-8146-e0fb7556d39a', 'uploads', 'd2Xj5NRqll8LObSoSSaujXYGkkAuFY-metaMjAyMS1Ib25kYS1QQ1gxMjUtU2Nvb3Rlci1yZXZpZXctZGV0YWlscy1wcmljZS1zcGVjXzEwMS5qcGc=-', 'd2Xj5NRqll8LObSoSSaujXYGkkAuFY-metaMjAyMS1Ib25kYS1QQ1gxMjUtU2Nvb3Rlci1yZXZpZXctZGV0YWlscy1wcmljZS1zcGVjXzEwMS5qcGc=-.jpg', 'image/jpeg', 'public', 'public', '63880', '[]', '[]', '{\"thumb200x200\": true}', '[]', '1', '2023-04-24 15:49:33', '2023-04-24 15:49:34'),
('11', 'App\\Models\\UploadTest', '1', 'fa17b929-56ae-4423-b9ac-994b83c949c5', 'uploads', 'test-upload-curl', '80085caa-3453-48ed-afaa-6e9d7a6388fc.txt', 'text/plain', 'spaces', 'spaces', '17', '[]', '[]', '[]', '[]', '1', '2026-02-13 21:56:05', '2026-02-13 21:56:05'),
('12', 'App\\Models\\UploadTest', '1', '8a2926c3-3179-4435-87c0-17be3f32e2ee', 'uploads', 'test-upload-curl', 'c0b0ffad-cc29-4f69-a0ec-788aa98da388.txt', 'text/plain', 'spaces', 'spaces', '17', '[]', '[]', '[]', '[]', '2', '2026-02-13 21:57:06', '2026-02-13 21:57:06'),
('13', 'App\\Models\\UploadTest', '1', '3e1b3e8f-9f08-4aa4-bb67-08fc0932916a', 'uploads', 'Test File', 'test-698faa1945613.txt', 'text/plain', 'spaces', 'spaces', '32', '[]', '[]', '[]', '[]', '3', '2026-02-13 22:47:53', '2026-02-13 22:47:53'),
('14', 'App\\Models\\UploadTest', '1', '40083338-ff84-4caf-8f82-d4be81e39f20', 'uploads', 'test-upload-doc', 'a9253b0b-b2ed-4190-8162-ede4e157f362.txt', 'text/plain', 'spaces', 'spaces', '17', '[]', '[]', '[]', '[]', '4', '2026-02-15 21:18:06', '2026-02-15 21:18:06');
SQL
        );
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
