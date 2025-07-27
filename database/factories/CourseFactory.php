<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'course_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived', 'pending_approval']),
            'is_paid' => $this->faker->boolean(),
            'start_date' => $this->faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
