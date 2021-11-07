<?php

namespace Database\Seeders;

use App\Models\Product;
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
        foreach ($this->json() as $item) {
            Product::create([
                "id" => $item['id'],
                "name" => $item['name'],
                "category" => $item['category'],
                "price" => $item['price'],
                "stock" => $item['stock'],
            ]);
        }
    }

    private function json()
    {
        return json_decode('[
            {
                "id": 100,
                "name": "Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti",
                "category": 1,
                "price": "120.75",
                "stock": 10
            },
            {
                "id": 101,
                "name": "Reko Mini Tamir Hassas Tornavida Seti 32\'li",
                "category": 1,
                "price": "49.50",
                "stock": 10
            },
            {
                "id": 102,
                "name": "Viko Karre Anahtar - Beyaz",
                "category": 2,
                "price": "11.28",
                "stock": 10
            },
            {
                "id": 103,
                "name": "Legrand Salbei Anahtar, Alüminyum",
                "category": 2,
                "price": "22.80",
                "stock": 10
            },
            {
                "id": 104,
                "name": "Schneider Asfora Beyaz Komütatör",
                "category": 2,
                "price": "12.95",
                "stock": 10
            }
        ]', true);
    }
}
