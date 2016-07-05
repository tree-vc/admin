<?php

use Illuminate\Database\Seeder;
use TestDataSeeders\TestDataSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        if(app()->isLocal()){
            $this->call(TestDataSeeder::class);
        }
    }
}
