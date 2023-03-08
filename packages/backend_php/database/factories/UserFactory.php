<?php

namespace Database\Factories;

use App\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
//        $faker = new Faker();

        return [
            'email' => $this->faker->email,
            'code' => $this->faker->text(10),
            'name' => $this->faker->name,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}