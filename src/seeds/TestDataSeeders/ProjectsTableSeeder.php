<?php

namespace TestDataSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\System\CommonService;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->delete();
        $id = \App\Models\Admin::first()->id;
        $appType = function(){
            $result = collect(app(CommonService::class)->projectAppTypes())->random(rand(1,3));
            $result = $result instanceof \Illuminate\Support\Collection ? $result->all() : $result;
            return $result;
        };
        for($i=0;$i<50;$i++){
            $publishStatus = collect([0,20,30])->random(1);
            $status        = collect([0,20])->random(1);

            $now = date('Y-m-d H:i:s');

            $data = [
                'publish_status'    =>  $publishStatus,
                'status'            =>  $status,
                'title'             =>  str_random(10),
                'type'              =>  rand(0,1),
                'app_type'          =>  call_user_func($appType),
                'part_a_budget'     =>  rand(100,1000),
                'part_a_develop_time'   =>  rand(10,50),
                'part_a_province'   =>  '北京',
                'part_a_city'       =>  '北京',
                'part_a_desc'       =>  str_random(30),
                'part_a_refer'      =>  str_random(20),
                'part_a_contact'    =>  str_random(10),
                'part_a_phone'      =>  str_random(11),
                'part_a_email'      =>  'test@test.com',
                'project_desc'      =>  str_random(30),
                'project_recruit'   =>  str_random(40),
                'project_locale'    =>  str_random(20),
                'deliver_result'    =>  str_random(15),
                'project_price'     =>  rand(100,1000),
                'project_develop_time'  =>  rand(10,50),
                'manager'           =>  'admin',
                'manager_phone'     =>  str_random(11),
                'attachments'       =>  '',
                'image'             =>  '',
                'audit_comment'     =>  str_random(35),
                'editor_id'         =>  $id,
                'auditor_id'        =>  $id,
                'edited_at'         =>  $now,
                'audited_at'        =>  $now,
            ];
            if($publishStatus === 20) $data['published_at'] = $now;
            if($status === 20) $data['allocate_at'] =$now;
            if($publishStatus === 30) $data['withdrawn_at'] = $now;

            \App\Models\Project::create($data);
        }

    }
}
