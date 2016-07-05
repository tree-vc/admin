<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Xiaoshu\Admin\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $admin = new Admin([
            'name'      =>  'admin',
            'real_name' =>  'administer',
            'email'     =>  'admin@treevc.net',
            'password'  =>  bcrypt('secret'),
        ]);
        $admin->supervisor = true;
        $admin->save();
        $system = new Admin([
            'name'      =>  'test',
            'real_name' =>  'test_administer',
            'email'     =>  'test@treevc.net',
            'password'  =>  bcrypt('secret'),
        ]);
        $system->supervisor = false;
        $system->save();

    }
}
