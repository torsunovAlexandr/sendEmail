<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserActionsSeeder extends Seeder
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
            DB::table('user_actions')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM  user_actions');
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE  users CASCADE');
        }

        $actions = [
            [
                'user_id'      => 4,
                'action_id'    => 1
            ],
            [
                'user_id'      => 2,
                'action_id'    => 1
            ],
        ];

        DB::table('user_actions')->insert($actions);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
