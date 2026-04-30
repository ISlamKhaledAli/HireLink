<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => fn () => Category::create([
                'name' => fake()->unique()->jobTitle(),
                'slug' => Str::slug(fake()->unique()->words(3, true)),
            ])->id,
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'work_type' => fake()->randomElement(['remote', 'onsite', 'hybrid']),
            'salary_range' => '$80,000 - $120,000',
            'deadline' => now()->addMonth()->toDateString(),
            'status' => 'approved',
        ];
    }
}
