<?php

namespace Xiaoshu\Admin\Controllers\Backend;

use Xiaoshu\Foundation\Result\Result;
use Illuminate\Http\Request;

use App\Http\Requests;
use Xiaoshu\Admin\Controllers\AdminController as Controller;
use Xiaoshu\Admin\Services\Logic\BackendRoleService;
use Illuminate\Support\Facades\Validator;

class AdminRoleController extends Controller
{
    public function index(BackendRoleService $service)
    {
        $page  = $this->request->input('page',1);
        $order = $this->request->input('order','desc');
        $roles = $service->paginateRoles($page ,$order);

        $this->request->fullUrl();
        return view('xiaoshu::backend.roles.index')->with([
            'roles'     =>  $roles,
            'inputs'    =>  $this->request->all(),
        ]);
    }

    public function create()
    {
        return view('xiaoshu::backend.roles.create');
    }

    public function store(BackendRoleService $service )
    {
        $validator = Validator::make($this->request->all(),[
            'title'  =>  'required',
        ]);
        if($validator->fails()){
            return back()->withInput()->withErrors('提交失败');
        }

        $title =  $this->request->input('title');
        $nodes =  $this->service->authorizeRoutes($this->request->input('nodes',[]));

        $editor = $this->request->user(REQUEST_FROM);

        $result = $service->createRole($editor,$title,$nodes);

        if($result->isSuccess()){
            return $this->redirectToIndex();
        } else {
            return back()->withInput()->withErrors($result->msg);
        }

    }

    public function edit(BackendRoleService $service ,$id)
    {
        $role = $service->findRoleOrFail($id);
        return view('xiaoshu::backend.roles.edit')->with('role',$role);
    }

    public function update(BackendRoleService $service , $id)
    {
        $editor = $this->request->user(REQUEST_FROM);

        $title  = $this->request->input('title');
        $nodes  = $this->service->authorizeRoutes($this->request->input('nodes',[]));

        $result = $service->updateRoleOrFail($id , $editor , [
            'title' =>  $title,
            'nodes' =>  $nodes,
        ]);

        if($result->isSuccess()){
            return $this->redirectToIndex();
        } else {
            return back()->withErrors($result->msg);
        }
    }

    public function destroy(BackendRoleService $service , $id)
    {
        $editor = $this->request->user(REQUEST_FROM);
        $result = $service->deleteRole($editor , $id);

        if($this->request->ajax() || $this->request->wantsJson()){
            return response()->json($result);
        }
        return view('xiaoshu::backend.backend')->with('message',$result->msg);
    }

    protected function redirectToIndex()
    {
        return redirect(route('backend::system.authorize.roles.index'));
    }
}
