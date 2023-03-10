<?php

namespace Database\Factories;

use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Topic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->regexify('[A-Z]{5}'),
            'title' => $this->faker->sentence,
            'description' => $this->faker->text(50),
            'limit' => $this->faker->numberBetween(1, 3),
            'lecturer_id' => User::role(Role::ROLE_LECTURER)->get()->random(1)->first()->id,
        ];
    }
}