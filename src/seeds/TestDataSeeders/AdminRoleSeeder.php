<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use App\Models\AdminRole;
use App\Models\BackendRole;
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
        $admin = \App\Models\Admin::where('name','test')->first();
        $role  = $roles->last();
        AdminRole::create([
            'admin_id'  =>  $admin->id,
            'role_id'   =>  $role->id,
        ]);

    }
}
