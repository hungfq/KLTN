<?php

namespace Database\Seeders;

use App\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'email' => 'quanghungpham07@gmail.com',
            'code' => 'quanghungpham07',
            'name' => 'HUNG ADMIN',
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'email' => 't.hungpham2314@gmail.com',
            'code' => 'hung.gv',
            'name' => 'HUNG LECTURER',
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 3,
            'email' => '19110373@student.hcmute.edu.vn',
            'code' => '19110373',
            'name' => 'Pham Quang Hung',
            'status' => User::STATUS_ACTIVE,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        DB::table('user_has_roles')->insert([
            'user_id' => 1,
            'role_id' => 1,
            'model_type' => 'App\Entities\User',
        ]);

        DB::table('user_has_roles')->insert([
            'user_id' => 2,
            'role_id' => 2,
            'model_type' => 'App\Entities\User',
        ]);

        DB::table('user_has_roles')->insert([
            'user_id' => 3,
            'role_id' => 3,
            'model_type' => 'App\Entities\User',
        ]);
    }
}
