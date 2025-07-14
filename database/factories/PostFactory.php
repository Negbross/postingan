<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),

            'title' => $title,
            'slug' => \Str::slug($title),
            'content' => $this->faker->paragraphs(10, true),
            'thumbnail' => null,
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'references' => collect(range(1, rand(2, 4)))->map(fn() => [
                'title' => $this->faker->sentence(5),
                'link' => $this->faker->url,
            ])->all(),
        ];
    }

    public function configure(): PostFactory|Factory
    {
        return $this->afterCreating(function (Post $post) {
            $tags = Tag::inRandomOrder()->limit(rand(2, 5))->get();
            $post->tags()->attach($tags);
        });
    }
}
