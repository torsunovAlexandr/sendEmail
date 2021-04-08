<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionsSeeder extends Seeder
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
            DB::table('actions')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM  actions');
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE  users CASCADE');
        }

        $actions = [
            [
                'title'         => 'Скидки 20% на бытовую технику',
                'date_start'    => Carbon::create(2020, 8, 28),
                'date_end'      => Carbon::create(2020, 9, 5),
            ],
        ];

        DB::table('actions')->insert($actions);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
