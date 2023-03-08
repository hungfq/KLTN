<?php

namespace Database\Seeders;

use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use Database\Factories\TopicFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Auth::login(User::find(1));

        DB::transaction(function () {
            $oldTopic = Topic::all();
            $students = User::role(Role::ROLE_STUDENT)->get();

            $factory = new TopicFactory();
            $factory->count(10)->create();

            Topic::whereNotIn('id', array_column($oldTopic->toArray(), 'id'))
                ->get()
                ->each(function ($topic) use ($students) {
                    $topic->students()->attach(
                        $students->random(1)->pluck('id')->toArray()
                    );
                });
        });
    }
}
