<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use Xiaoshu\Admin\Models\AdminRole;
use Xiaoshu\Admin\Models\BackendRole;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('admin_roles')->delete();
        $roles = BackendRole::all();
        $admin = Xiaoshu\Admin\Models\Admin::where('name','test')->first();
        $role  = $roles->last();
        AdminRole::create([
            'admin_id'  =>  $admin->id,
            'role_id'   =>  $role->id,
        ]);

    }
}
