<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Авто'],
            ['name' => 'Недвижимость'],
            ['name' => 'Услуги'],
            ['name' => 'Электроника'],
            ['name' => 'Одежда'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}