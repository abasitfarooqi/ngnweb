<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Accessories
            ['id' => 2, 'name' => 'Phone & Device Mounts', 'description' => 'Mounts for phones and other devices', 'image_url' => 'phone_device_mounts.jpg'],
            ['id' => 3, 'name' => 'Covers', 'description' => 'Protective covers for various uses', 'image_url' => 'covers.jpg'],
            ['id' => 4, 'name' => 'Handlebar Accessories', 'description' => 'Accessories for handlebars', 'image_url' => 'handlebar_accessories.jpg'],
            ['id' => 5, 'name' => 'Battery Care & Power Accessories', 'description' => 'Battery maintenance and power accessories', 'image_url' => 'battery_care_power_accessories.jpg'],
            ['id' => 6, 'name' => 'Scoot Stuff', 'description' => 'Accessories for scooters', 'image_url' => 'scoot_stuff.jpg'],
            ['id' => 7, 'name' => 'Workshop', 'description' => 'Tools and equipment for workshops', 'image_url' => 'workshop.jpg'],
            ['id' => 8, 'name' => 'Helmet Accessories', 'description' => 'Accessories for helmets', 'image_url' => 'helmet_accessories.jpg'],
            ['id' => 9, 'name' => 'Lighting', 'description' => 'Lighting products and accessories', 'image_url' => 'lighting.jpg'],
            ['id' => 10, 'name' => 'Paint Protection', 'description' => 'Products to protect vehicle paint', 'image_url' => 'paint_protection.jpg'],
            ['id' => 11, 'name' => 'Travel & Transportation', 'description' => 'Travel and transportation accessories', 'image_url' => 'travel_transportation.jpg'],
            ['id' => 12, 'name' => 'Tyre & Wheel Care', 'description' => 'Tyre and wheel maintenance products', 'image_url' => 'tyre_wheel_care.jpg'],
            ['id' => 13, 'name' => 'Eye Wear', 'description' => 'Eyewear and accessories', 'image_url' => 'eye_wear.jpg'],

            // Luggage
            ['id' => 14, 'name' => 'Helmet & Boot Carriers', 'description' => 'Carriers for helmets and boots', 'image_url' => 'helmet_boot_carriers.jpg'],
            ['id' => 15, 'name' => 'Panniers', 'description' => 'Side bags for motorcycles', 'image_url' => 'panniers.jpg'],
            ['id' => 16, 'name' => 'Luggage Accessories', 'description' => 'Accessories for luggage', 'image_url' => 'luggage_accessories.jpg'],
            ['id' => 17, 'name' => 'Backpacks', 'description' => 'Backpacks for travel and daily use', 'image_url' => 'backpacks.jpg'],
            ['id' => 18, 'name' => 'Tail Packs', 'description' => 'Storage packs for the tail of motorcycles', 'image_url' => 'tail_packs.jpg'],
            ['id' => 19, 'name' => 'Tank Bags', 'description' => 'Bags designed to fit on motorcycle tanks', 'image_url' => 'tank_bags.jpg'],
            ['id' => 20, 'name' => 'Waist & Leg Bags', 'description' => 'Bags worn around the waist or leg', 'image_url' => 'waist_leg_bags.jpg'],
            ['id' => 21, 'name' => 'Top Boxes', 'description' => 'Storage boxes for the top of motorcycles', 'image_url' => 'top_boxes.jpg'],
            ['id' => 22, 'name' => 'Sat Nav Holder', 'description' => 'Holders for satellite navigation devices', 'image_url' => 'sat_nav_holder.jpg'],

            // Security
            ['id' => 23, 'name' => 'Lever Locks', 'description' => 'Locks for levers on motorcycles', 'image_url' => 'lever_locks.jpg'],
            ['id' => 24, 'name' => 'Chain Locks & Chains', 'description' => 'Chains and chain locks for securing vehicles', 'image_url' => 'chain_locks_chains.jpg'],
            ['id' => 25, 'name' => 'Anchors', 'description' => 'Anchors for securing vehicles to fixed objects', 'image_url' => 'anchors.jpg'],
            ['id' => 26, 'name' => 'Cable Locks', 'description' => 'Flexible cable locks for security', 'image_url' => 'cable_locks.jpg'],
            ['id' => 27, 'name' => 'Disc Locks & Padlocks', 'description' => 'Disc locks and padlocks for motorcycles', 'image_url' => 'disc_locks_padlocks.jpg'],
            ['id' => 28, 'name' => 'U Locks', 'description' => 'U-shaped locks for securing motorcycles', 'image_url' => 'u_locks.jpg'],

            // Helmets
            ['id' => 29, 'name' => 'Helmets', 'description' => 'Various types of helmets', 'image_url' => 'helmets.jpg'],

            // Rider Wear
            ['id' => 30, 'name' => 'Rider Wear', 'description' => 'Apparel for riders', 'image_url' => 'rider_wear.jpg'],

            // Tracker
            ['id' => 31, 'name' => 'Tracker', 'description' => 'Tracking devices and systems', 'image_url' => 'tracker.jpg'],
        ];

        DB::table('ngn_categories')->insert($categories);
    }
}
