<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(AdminsTableSeeder::class);
        //$this->call(DevelopersTableSeeder::class);
        $this->call(BackendRoleSeeder::class);
        $this->call(AdminRoleSeeder::class);
        //$this->call(ProjectsTableSeeder::class);
        //$this->call(DeveloperApplyTestSeeder::class);
        Model::reguard();
    }
}
