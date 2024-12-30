<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('markets')->insert([
            [
                'name' => 'Market 1',
                'location' => 'Location 1',
                'phone' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Market 2',
                'location' => 'Location 2',
                'phone' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more market entries here
        ]);
    }
}
