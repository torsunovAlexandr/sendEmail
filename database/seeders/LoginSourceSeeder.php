<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class loginSourceSeeder extends Seeder
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
            DB::table('login_source')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM  users');
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE  users CASCADE');
        }


        //Add the master administrator, user id of 1
        $users = [
            [
                'user_id'   => '1',
                'tms'       => Carbon::now(),
                'source'    => 'site',
            ],
            [
                'user_id'   => '3',
                'tms'       => Carbon::create(2021, 03, 5, 12, 0, 0),
                'source'    => 'site',
            ],
            [
                'user_id'   => '2',
                'tms'       => Carbon::create(2021, 03, 7, 12, 0, 0),
                'source'    => 'site',
            ],
            [
                'user_id'   => '2',
                'tms'       => Carbon::create(2021, 03, 9, 12, 0, 0),
                'source'    => 'site',
            ],
            [
                'user_id'   => '2',
                'tms'       => Carbon::now(),
                'source'    => 'site',
            ],
            [
                'user_id'   => '4',
                'tms'       => Carbon::create(2020, 9, 1, 12, 0, 0),
                'source'    => 'site',
            ],
            [
                'user_id'   => '4',
                'tms'       => Carbon::create(2021, 03, 11, 12, 0, 0),
                'source'    => 'site',
            ],

        ];

        DB::table('login_source')->insert($users);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}

