<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(10, true),
            'thumbnail' => $this->faker->optional(0.7)->imageUrl(800, 600, 'business', true),
            'author' => $this->faker->name(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the blog has no thumbnail.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutThumbnail()
    {
        return $this->state(function (array $attributes) {
            return [
                'thumbnail' => null,
            ];
        });
    }

    /**
     * Indicate that the blog has a specific author.
     *
     * @param  string  $author
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAuthor(string $author)
    {
        return $this->state(function (array $attributes) use ($author) {
            return [
                'author' => $author,
            ];
        });
    }

    /**
     * Indicate that the blog was created recently.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function recent()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }
}