<?php

namespace Xiaoshu\Admin\Controllers;

use Xiaoshu\Admin\Services\System\AdminNodeService;
use Xiaoshu\Foundation\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;

class AdminController extends Controller
{
    protected $service;


    public function __construct(AdminNodeService $service , Request $request){
        $this->service = $service;
        parent::__construct($request);
    }


    protected function log($method , $inputs)
    {
        $user  = $this->getUser();
        Log::info('admin '.$user->id.' do '.$method,$inputs);
    }
}

