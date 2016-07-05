<?php
/**
 * Created by PhpStorm.
 * User: qzhang
 * Date: 2016/6/28
 * Time: 15:18
 */

/**
 * 后台通用路由
 */
$backendCommonRoutes = function($router){

    //管理员登录
    $router->get('/',[
        'uses'  =>  'AuthController@getLogin',
        'as'    =>  'auth.login',
    ]);
    $router->get('auth/login',[
        'uses'  =>  'AuthController@getLogin',
        'as'    =>  'auth.login',
    ]);

    $router->post('auth/login',[
        'uses'  =>  'AuthController@postLogin',
        'as'    =>  'auth.post-login',
    ]);
    //登出
    $router->get('auth/logout',[
        'uses'  =>  'AuthController@getLogout',
        'as'    =>  'auth.logout',
    ]);
    //重置密码页
    $router->get('auth/reset-password',[
        'uses'  =>  'PasswordController@getResetPassword',
        'as'    =>  'auth.reset-password',
    ]);

    //重置密码页表单提交
    $router->post('auth/reset-password',[
        'uses'  =>  'PasswordController@postResetPassword',
        'as'    =>  'auth.post-reset-password',
    ]);
};

/**
 * 后台登录后使用的路由
 */
$backendAdminRoutes = function($router){

    //后台个人分组
    $router->group([
        'prefix'    =>  'home',
    ],function($router){

        //管理组首页
        $router->get('/',[
            'uses'      =>  'IndexController@homeIndex',
            'as'        =>  'home.index',
        ]);



    });

    //后台系统分组
    $router->group([
        'prefix'    => 'system',
    ],function($router){


        //管理组首页
        $router->get('/',[
            'uses'      =>  'IndexController@systemIndex',
            'as'        =>  'system.index',
        ]);


        //管理员模块
        //管理员管理
        $router->resource('admin/admins','AdminController',[
            'except'    =>  [
                'destroy','create','show'
            ],
        ]);
        //操作日志
        //权限模块
        //角色权限
        $router->resource('authorize/roles','AdminRoleController',[
            'except'    =>  ['show'],
        ]);

        //管理员管理

        //角色管理
    });

    //后台管理分组
    $router->group([
        'prefix'    =>  'manage',
    ],function($router){

        //管理组首页
        $router->get('/',[
            'uses'      =>  'IndexController@manageIndex',
            'as'        =>  'manage.index',
        ]);
    });

    //系统设置

    //个人设置

};


/**
 * 注册后台通用路由
 */
$router->group([
    'as'            =>  'backend::',
    'middleware'    =>  'web',
    'domain'        =>  config('domain.backend'),
    'namespace'     =>  'Xiaoshu\Admin\Controllers\Backend',
],$backendCommonRoutes);


/**
 * 注册后台管理员路由
 */
$router->group([
    'middleware'    =>  [
        'web',
        'auth:backend',
        'backend.authorize',
    ],
    'as'            =>  'backend::',
    'domain'        =>  config('domain.backend'),
    'namespace'     =>  'Xiaoshu\Admin\Controllers\Backend',
],$backendAdminRoutes);

$router->get('/common/regionjs', function(){
    return Xiaoshu\Foundation\Supports\RegionJs::getJs();
});
