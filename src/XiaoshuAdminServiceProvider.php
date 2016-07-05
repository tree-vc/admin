<?php
/**
 * Created by PhpStorm.
 * User: qzhang
 * Date: 2016/6/28
 * Time: 14:56
 */

namespace Xiaoshu\Admin;

use Xiaoshu\Admin\Models\Admin;
use Xiaoshu\Admin\Services\System\AdminNodeService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;

class XiaoshuAdminServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        /* 使用说明 */
        /*
         * 在 App/Http/Kernel.php 中的 $routeMiddleware中加入以下这行
         * 'backend.authorize'     => \App\Http\Middleware\BackendAuthorize::class,
         * view 要自己手动复制
         * 参考本组件下的routes.php配置路由
         * 配置以下三个文件
         * config/backend.php
         * config/domain.php
         * config/adminmenu.php
         * 运行 artisan migrate:install
         * 运行 artisan db:seed
         */

        /*if (!$this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }*/

        //$this->loadViewsFrom(__DIR__.'/views', 'xiaoshu');
        /*$this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/xiaoshu'),
        ]);*/

        //public
        $this->publishes([
            __DIR__.'/public' => public_path('xiaoshu.admin'),
        ]);

        //migrations
        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ]);

        //seeds
        $this->publishes([
            __DIR__.'/seeds' => database_path('seeds'),
        ]);

        define('REQUEST_FROM','backend');

        //config/backend.php
        //config/domain.php
        //config/adminmenu.php
        $this->publishes([
            __DIR__.'/config' => config_path(),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->registerAdminService();
        $guards = config('auth.guards');
        $guards['backend'] = [
            'driver' => 'session',
            'provider' => 'admins',
        ];
        config([
            'auth.guards' => $guards
        ]);

        $providers = config('auth.providers');
        $providers['admins'] = [
            'driver' => 'eloquent',
            'model' => Admin::class,
        ];
        config([
            'auth.providers' => $providers,
        ]);
        //dd(config('auth.guards'));
    }

    /*protected function registerAdminService()
    {
        $this->app->singleton(AdminNodeService::class,function($app){
            return new AdminNodeService($app->make(Router::class) , $app->make(Request::class));
        });
    }*/
}