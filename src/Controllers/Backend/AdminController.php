<?php

namespace Xiaoshu\Admin\Controllers\Backend;


use Xiaoshu\Foundation\Result\Result;
use App\Http\Requests;
use Xiaoshu\Admin\Controllers\AdminController as Controller;
use Xiaoshu\Admin\Services\AdminService;
use Xiaoshu\Admin\Services\Logic\BackendRoleService;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index(AdminService $service ,BackendRoleService $roleService)
    {
        //input
        $status = $this->request->input('status','all');

        $orders = [];
        if($this->request->input('orderByName')){
            $orders['name'] = $this->request->input('orderByName');
        }
        if($this->request->input('orderByRealName')){
            $orders['real_name'] = $this->request->input('orderByRealName');
        }

        $page  = $this->request->input('page',1);

        //service
        $admins = $service->listAdmins($page,$status,$orders,10);
        $roles  = $roleService->getAllRolesArray();

        //response
        return response()->view('xiaoshu::backend.admins.index',[
            'admins'        => $admins,
            'statusOptions' => $service->getStatusOptions(),
            'backendRoles'  => $roles,
        ]);
    }


    public function store(AdminService $service)
    {
        //表单验证
        $validator = Validator::make($this->request->all(),[
            'name'      =>  'required',
            'real_name' =>  'required',
            'email'     =>  'required|email',
            'password'  =>  'required|confirmed',
        ]);

        if($validator->fails()){
            if($this->wantsJson()){
                return Result::fail('表单验证未通过')->toArray();
            }
            return back()->withInput()->withErrors('表单验证未通过');
        }

        //组装参数
        $editor =  $this->request->user(REQUEST_FROM);
        $data   =  $this->request->only(['name','real_name','email','password']);
        $roles  =  $this->request->input('role_ids',[]);

        //调用服务
        $result =  $service->createAdmin($editor , $data , $roles);

        //返回结果
        if($this->wantsJson()){
            return $result->toArray();
        } else if($result->isFailed()){
            return back()->withInput()->withErrors($result->msg);
        } else {
            return $this->redirectIndex();
        }

    }

    public function edit(AdminService $service ,BackendRoleService $roleService, $id)
    {

        $admin  = $service->findOrFail($id);
        $roles  = $roleService->getAllRolesArray();

        return response()->view('xiaoshu::backend.admins.edit',[
            'id'            =>  $id,
            'admin'         =>  $admin,
            'backendRoles'  =>  $roles,
            'statusTexts'   =>  $service->getStatusTexts(),
        ]);
    }

    public function update(AdminService $service , $id)
    {
        //校验
        if(!$service->adminExists($id)){
            abort(404);
        }

        //分配给子方法
        $func = $this->request->input('_func','');
        $method = 'update'.ucfirst($func);

        if($func && method_exists($this,$method)){
            return call_user_func_array([$this,$method],[$service,$id]);
        }


        //正常的update
        //校验参数
        $validator = Validator::make($this->request->all(),[
            'email'     =>  'email',
            'password'  =>  'confirmed',
        ]);

        if($validator->fails()){
            return back()->withInput($this->request->only(['email']))->withErrors('更新系统用户失败');
        }

        //调用服务
        $editor = $this->request->user(REQUEST_FROM);
        $result = $service->updateAdmin(
            $editor ,
            $id ,
            $this->request->input('email'),
            $this->request->input('password'),
            $this->request->input('status'),
            $this->request->input('roles',[])
        );

        //返回结果
        if($result->isFailed()){
            return back()->withInput($this->request->only(['email']))->withErrors($result->msg);
        } else {
            return $this->redirectIndex();
        }
    }

    public function updateDestroy(AdminService $service , $id)
    {
        $editor = $this->request->user(REQUEST_FROM);
        $result = $service->softDeleteAdmin($editor , $id);

        if($this->wantsJson()){
            return $result->toArray();
        }

        return $this->redirectIndex();
    }

    public function updateLock(AdminService $service , $id)
    {
        $editor = $this->request->user(REQUEST_FROM);
        $result = $service->setAdminLock(true , $editor , $id);

        if($this->wantsJson()){
            return $result->toArray();
        }

        return $this->redirectIndex();
    }

    public function updateUnlock(AdminService $service , $id)
    {
        $editor = $this->request->user(REQUEST_FROM);
        $result = $service->setAdminLock(false , $editor , $id);

        if($this->wantsJson()){
            return $result->toArray();
        }

        return $this->redirectIndex();
    }

    protected function redirectIndex()
    {
        return redirect(route('backend::system.admin.admins.index'));
    }
}
