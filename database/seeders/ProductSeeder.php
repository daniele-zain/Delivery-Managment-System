<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'market_id' => 1, // Ensure this market ID exists in the markets table
                'name' => 'Product 1',
                'description' => 'This is the description for Product 1.',
                'price' => 10.99,
                'image_url' => 'https://example.com/images/product1.jpg',
                'quantity'=>90,
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
            [
                'market_id' => 1,
                'name' => 'Product 2',
                'description' => 'This is the description for Product 2.',
                'price' => 15.49,
                'image_url' => 'https://example.com/images/product2.jpg',
                'quantity'=>80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'market_id' => 2, // Ensure this market ID exists in the markets table
                'name' => 'Product 3',
                'description' => 'This is the description for Product 3.',
                'price' => 8.99,
                'image_url' => null, // No image for this product
                'quantity'=>90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
