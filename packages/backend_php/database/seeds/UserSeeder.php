<?php

namespace Database\Seeders;

use App\Entities\Role;
use App\Entities\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
            $roles = Role::query()->where('name', '!=', Role::ROLE_ADMIN)->get();
            $oldUser = User::all();

            $factory = new UserFactory();
            $factory->count(10)->create();

            User::whereNotIn('id', array_column($oldUser->toArray(), 'id'))
                ->get()
                ->each(function ($user) use ($roles) {
                    $user->roles()->attach(
                        $roles->random(1)->pluck('id')->toArray()
                    );
                });
        });
    }
}
