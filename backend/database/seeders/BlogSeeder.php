<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 blog posts
        Blog::factory()->count(50)->create();

        // Create some specific blog posts
        Blog::factory()->create([
            'title' => 'Getting Started with Laravel',
            'slug' => 'getting-started-with-laravel',
            'excerpt' => 'Learn how to start your first Laravel project with this comprehensive guide.',
            'content' => $this->getLaravelContent(),
            'author' => 'Taylor Otwell',
            'created_at' => now()->subDays(10),
        ]);

        Blog::factory()->create([
            'title' => 'Best Practices for API Development',
            'slug' => 'best-practices-api-development',
            'excerpt' => 'Discover the best practices for building robust and scalable APIs.',
            'content' => $this->getAPIContent(),
            'author' => 'John Doe',
            'created_at' => now()->subDays(5),
        ]);

        Blog::factory()->create([
            'title' => 'Introduction to Vue.js 3',
            'slug' => 'introduction-to-vuejs-3',
            'excerpt' => 'Learn the basics of Vue.js 3 and how to build modern web applications.',
            'content' => $this->getVueContent(),
            'author' => 'Evan You',
            'created_at' => now()->subDays(2),
        ]);

        $this->command->info('✅ Blog posts seeded successfully!');
        $this->command->info('Total blogs created: ' . Blog::count());
    }

    private function getLaravelContent(): string
    {
        return <<<CONTENT
        <h2>Introduction to Laravel</h2>
        <p>Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.</p>
        
        <h3>Installation</h3>
        <p>First, install Laravel using Composer:</p>
        <pre><code>composer create-project laravel/laravel example-app</code></pre>
        
        <h3>Basic Configuration</h3>
        <p>After installing Laravel, you should configure your database settings in the .env file.</p>
        
        <h3>Routing</h3>
        <p>All Laravel routes are defined in your route files, which are located in the routes directory.</p>
        
        <h3>Conclusion</h3>
        <p>Laravel provides a clean and elegant framework for building modern web applications.</p>
        CONTENT;
    }

    private function getAPIContent(): string
    {
        return <<<CONTENT
        <h2>API Development Best Practices</h2>
        <p>Building a good API is crucial for modern web applications. Here are some best practices:</p>
        
        <h3>1. Use RESTful Principles</h3>
        <p>Follow REST conventions for predictable and clean API design.</p>
        
        <h3>2. Version Your API</h3>
        <p>Always version your API to maintain backward compatibility.</p>
        
        <h3>3. Implement Proper Error Handling</h3>
        <p>Return meaningful error messages with appropriate HTTP status codes.</p>
        
        <h3>4. Use Pagination</h3>
        <p>For large datasets, implement pagination to improve performance.</p>
        
        <h3>5. Secure Your API</h3>
        <p>Implement authentication, authorization, and rate limiting.</p>
        CONTENT;
    }

    private function getVueContent(): string
    {
        return <<<CONTENT
        <h2>Vue.js 3: The Progressive Framework</h2>
        <p>Vue.js is an approachable, performant and versatile framework for building web user interfaces.</p>
        
        <h3>Composition API</h3>
        <p>The Composition API is a new feature in Vue 3 that provides better logic reuse and code organization.</p>
        
        <h3>Setup Script</h3>
        <p>Vue 3 introduces the &lt;script setup&gt; syntax for more concise component authoring.</p>
        
        <h3>Reactivity System</h3>
        <p>Vue 3's reactivity system has been completely rewritten for better performance.</p>
        
        <h3>Getting Started</h3>
        <p>To start with Vue 3, you can use Vite for a fast development experience.</p>
        CONTENT;
    }
}