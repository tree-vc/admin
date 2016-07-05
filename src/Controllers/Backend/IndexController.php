<?php

namespace Xiaoshu\Admin\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function manageIndex()
    {
        return redirect(route('backend::system.admin.admins.index'));
    }


    public function systemIndex()
    {
        return view('xiaoshu::backend.backend');
    }

    public function homeIndex()
    {
        return view('xiaoshu::backend.backend');
    }
}
