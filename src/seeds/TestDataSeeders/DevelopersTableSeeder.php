<?php

namespace TestDataSeeders;

use App\Core\Model;
use Illuminate\Database\Seeder;
use App\Models\Developer;
use Illuminate\Support\Facades\DB;
use App\Services\System\CommonService;

/**
 * @author mzhu
 *
 * developers 表测试数据
 *
 * Class DevelopersTableSeeder
 */
class DevelopersTableSeeder extends Seeder
{

    protected $common;

    public function __construct(CommonService $common)
    {
        $this->common = $common;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('developers')->delete();

        $skills = $this->common->developerSkills();
        $levels = array_keys($this->common->educationLevels());
        $ages   = array_keys($this->common->workAges());
        for($i=0;$i<10;$i++){
            $skill = array_slice($skills,rand(1,3),3);
            Developer::create([
                'mobile'    => '1234567890'.$i,
                'name'      =>  'test'.$i,
                'password'  =>  bcrypt('secret'),
                'province'  =>  '北京',
                'city'      =>  '北京',
                'work_age'  =>  $ages[rand(1,2)],
                'education_level'   =>  $levels[rand(1,2)],
                'developer_skill'   =>  Model::arrayToField($skill),
            ]);
        }

    }
}
