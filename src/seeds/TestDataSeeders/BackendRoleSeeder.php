<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use Xiaoshu\Admin\Services\System\AdminNodeService as Service;
use Xiaoshu\Admin\Models\BackendRole;
use Illuminate\Support\Facades\DB;

/**
 * Class BackendRoleSeeder
 * @author zhuming
 */
class BackendRoleSeeder extends Seeder
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \Illuminate\Support\Facades\DB::table('backend_roles')->delete();

        $routesNames = $this->service->getRouteNames();
        BackendRole::create([
            'title' =>  '超级管理员',
            'nodes' =>  $routesNames,
        ]);

        BackendRole::create([
            'title' =>  '测试管理员',
            'nodes' =>  $this->service->authorizeRoutes(['backend::manage.index']),
        ]);

    }
}
