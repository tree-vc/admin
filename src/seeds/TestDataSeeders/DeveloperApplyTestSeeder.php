<?php
/**
 * Date: 16/5/29
 * Time: 下午10:31
 */

namespace TestDataSeeders;

use App\Models\Developer;
use App\Models\Project;
use App\Services\Logic\ProjectService;
use Illuminate\Database\Seeder;

class DeveloperApplyTestSeeder extends Seeder
{
    protected $project;

    public function __construct(ProjectService $project)
    {
        $this->project  = $project;

    }


    public function run()
    {
        $projects = $this->getProjects();
        $developers = Developer::paginate(4,['*'],'page',2);

        foreach($projects as $project){
            $this->insertApply($project,$developers);
        }

    }

    protected function getProjects()
    {
        return $this->project->listProjects([
            'publish_status'    =>  Project::STATUS_PUBLISH,
            'status'            =>  Project::STATUS_RECRUITING,
        ],1);
    }

    protected function insertApply($project,$developers)
    {
        foreach($developers as $developer){
            $project->appliers()->attach($developer->id,[
                'status'        =>  0,
                'created_at'    =>  date('Y-m-d H:i:s'),
            ]);
        }
    }
}