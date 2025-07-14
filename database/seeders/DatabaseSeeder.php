<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::truncate();
//        User::factory()->create([
//            'name' => 'divo',
//            'email' => 'divo@example.com',
//            'password' => Hash::make('123'),
//            'username' => 'divo',
//        ]);
        User::factory(10)->create();
        Category::factory(15)->create();
        Tag::factory(30)->create();

        Post::factory(30)->create();
        $this->call(SuperAdminSeeder::class);
    }
}
