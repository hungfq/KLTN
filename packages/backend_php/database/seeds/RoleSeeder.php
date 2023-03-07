<?php

namespace Database\Seeders;

use App\Entities\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => Role::ROLE_ADMIN,
                'guard_name' => 'api',
            ],
            [
                'id' => 2,
                'name' => Role::ROLE_LECTURER,
                'guard_name' => 'api',
            ],
            [
                'id' => 3,
                'name' => Role::ROLE_STUDENT,
                'guard_name' => 'api',
            ],
        ]);
    }
}
