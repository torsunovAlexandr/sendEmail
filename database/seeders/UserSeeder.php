<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon as Carbon;
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
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::table('users')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM  users');
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE  users CASCADE');
        }


        //Add the master administrator, user id of 1
        $users = [
            [
                'second_name'   => 'Pushkin',
                'first_name'    => 'Alexandr',
                'email'         => 'alexandrtorsunoff@yandex.ru',
            ],
            [
                'second_name'   => 'Golovach',
                'first_name'    => 'Elena',
                'email'         => 'torsunovalexandr@gmail.com',
            ],
            [
                'second_name'   => 'Mask',
                'first_name'    => 'Ilon',
                'email'         => 'IvanSecurity132@yandex.ru',
            ],
            [
                'second_name'   => 'Kadyrov',
                'first_name'    => 'Ramzan',
                'email'         => 'prekrasnyj.neznakomecz.00@mail.ru',
            ],

        ];

        DB::table('users')->insert($users);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
