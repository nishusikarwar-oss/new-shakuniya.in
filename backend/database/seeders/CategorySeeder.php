<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories
        Category::truncate();

        // Create parent categories
        $parentCategories = [
            [
                'category_name' => 'All',
                'description' => 'All products category',
                'status' => true,
                'order' => 0,
            ],
            [
                'category_name' => 'Product',
                'description' => 'Product category',
                'status' => true,
                'order' => 1,
            ],
            [
                'category_name' => 'UI',
                'description' => 'UI/UX design category',
                'status' => true,
                'order' => 2,
            ],
            [
                'category_name' => 'Dashboard',
                'description' => 'Dashboard templates and components',
                'status' => true,
                'order' => 3,
            ],
            [
                'category_name' => 'App',
                'description' => 'Mobile and web applications',
                'status' => true,
                'order' => 4,
            ],
        ];

        foreach ($parentCategories as $category) {
            Category::create([
                'category_name' => $category['category_name'],
                'slug' => Str::slug($category['category_name']),
                'description' => $category['description'],
                'status' => $category['status'],
                'order' => $category['order'],
            ]);
        }

        // Create child categories
        $productCategory = Category::where('category_name', 'Product')->first();
        if ($productCategory) {
            $childCategories = [
                [
                    'category_name' => 'Electronics',
                    'parent_id' => $productCategory->id,
                ],
                [
                    'category_name' => 'Clothing',
                    'parent_id' => $productCategory->id,
                ],
                [
                    'category_name' => 'Books',
                    'parent_id' => $productCategory->id,
                ],
            ];

            foreach ($childCategories as $child) {
                Category::create([
                    'category_name' => $child['category_name'],
                    'slug' => Str::slug($child['category_name']),
                    'parent_id' => $child['parent_id'],
                    'status' => true,
                ]);
            }
        }

        // Create additional random categories for testing
        Category::factory()
            ->count(20)
            ->parent()
            ->create();

        // Create child categories for random parents
        $parents = Category::whereNull('parent_id')->where('id', '>', 5)->get();
        foreach ($parents as $parent) {
            Category::factory()
                ->count(rand(1, 5))
                ->childOf($parent->id)
                ->create();
        }
    }
}