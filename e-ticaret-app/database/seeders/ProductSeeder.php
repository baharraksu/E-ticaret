<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kategoriler oluştur
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Elektronik ürünler'],
            ['name' => 'Giyim', 'slug' => 'giyim', 'description' => 'Giyim ve aksesuar'],
            ['name' => 'Kitap', 'slug' => 'kitap', 'description' => 'Kitaplar ve yayınlar'],
            ['name' => 'Ev & Yaşam', 'slug' => 'ev-yasam', 'description' => 'Ev ve yaşam ürünleri'],
        ];

        foreach ($categories as $categoryData) {
            \App\Models\Category::create($categoryData);
        }

        // Ürünler oluştur
        $products = [
            [
                'name' => 'iPhone 13 Pro',
                'description' => 'Apple iPhone 13 Pro 128GB, en son teknoloji ile donatılmış akıllı telefon.',
                'price' => 29999.99,
                'stock' => 25,
                'category_id' => 1,
                'image' => null,
            ],
            [
                'name' => 'Samsung Galaxy S21',
                'description' => 'Samsung Galaxy S21 5G, güçlü kamera ve hızlı işlemci.',
                'price' => 18999.99,
                'stock' => 18,
                'category_id' => 1,
                'image' => null,
            ],
            [
                'name' => 'Nike Air Max',
                'description' => 'Nike Air Max spor ayakkabı, rahat ve şık tasarım.',
                'price' => 1299.99,
                'stock' => 42,
                'category_id' => 2,
                'image' => null,
            ],
            [
                'name' => 'Adidas T-Shirt',
                'description' => 'Adidas orijinal t-shirt, %100 pamuk.',
                'price' => 299.99,
                'stock' => 67,
                'category_id' => 2,
                'image' => null,
            ],
            [
                'name' => 'Laravel Kitabı',
                'description' => 'Laravel framework öğrenme kitabı, kapsamlı rehber.',
                'price' => 89.99,
                'stock' => 15,
                'category_id' => 3,
                'image' => null,
            ],
            [
                'name' => 'Redis Öğreniyorum',
                'description' => 'Redis veritabanı öğrenme kitabı, pratik örneklerle.',
                'price' => 79.99,
                'stock' => 8,
                'category_id' => 3,
                'image' => null,
            ],
            [
                'name' => 'Philips Kahve Makinesi',
                'description' => 'Philips otomatik kahve makinesi, profesyonel kahve deneyimi.',
                'price' => 2499.99,
                'stock' => 12,
                'category_id' => 4,
                'image' => null,
            ],
            [
                'name' => 'IKEA Masa Sandalye',
                'description' => 'IKEA çalışma masası ve sandalye seti.',
                'price' => 899.99,
                'stock' => 23,
                'category_id' => 4,
                'image' => null,
            ],
        ];

        foreach ($products as $productData) {
            \App\Models\Product::create($productData);
        }
    }
}
