<?php

namespace Xiaoshu\Admin\Middleware;

use Closure;

use Xiaoshu\Admin\Services\System\AdminNodeService as Service;
use Illuminate\Http\Request;

class BackendAuthorize
{

    /**
     * @var \Illuminate\View\View|\Illuminate\Contracts\View\Factory;
     */
    protected $view;

    /**
     * @var Request;
     */
    protected $request;

    public function __construct(Service $service)
    {
        $this->service  = $service;
        $this->view     = view();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next)
    {
        $currRoute = $this->service->currRoute();
        if(is_null($currRoute)){
            abort(404);
        }


        if(!$currRoute->isAuthorized){
            abort(403,'Unauthorized.');
        }

        //绑定当前路由
        $currRootNode  = $this->service->currRootNode();
        $this->view->share('currRootNode',$currRootNode);
        $this->view->share('currRoute',$currRoute);
        $this->bindAdminViewData($request);


        return $next($request);
    }

    /**
     * @param Request $request
     */
    protected function bindAdminViewData($request)
    {
        $admin = $request->user(REQUEST_FROM);
        //绑定管理员信息
        $this->view->share('loginAdmin',$admin);

        //绑定顶部菜单
        //dd($this->service->buildNodeTree());
        $this->view->share('adminMenu',$this->service->buildNodeTree());
        $this->view->share('adminRoutes',$this->service->getAdminRoutes());


        //绑定侧边栏菜单

    }
}
