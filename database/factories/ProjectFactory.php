<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => fn () => Category::factory()->create(),
            'name' => $this->faker->sentence(3),
            'slug' => Str::slug(fake()->sentence(3)),
            'cost' => $this->faker->randomFloat(2, 0, 9999.99),
            // 'logo' => 'http://images.test/posts/'.random_int(0, 9).'.jpg',
            'info' => fake()->paragraph(1),
            'completed' => $this->faker->boolean,
        ];
    }
}
