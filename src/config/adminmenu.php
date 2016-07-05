<?php

$filePath = __DIR__.'/'.env('APP_ENV','local').'/adminmenu.php' ;

return file_exists($filePath) ? include $filePath :array (

    '系统管理' =>
        array (
            'router' => 'backend::system.index',
            '管理员' =>
                array (
                    '管理员管理' =>
                        array (
                            'router' => 'backend::system.admin.admins.index',
                            '创建' =>
                                array (
                                    'router' => 'backend::system.admin.admins.store',
                                ),
                            '编辑' =>
                                array (
                                    'router' => 'backend::system.admin.admins.edit',
                                ),
                            '更新' =>
                                array (
                                    'router' => 'backend::system.admin.admins.update',
                                ),
                            '删除' =>
                                array (
                                    'router' => 'backend::system.admin.admins.destroy',
                                ),
                        ),
                ),
            '权限' =>
                array (
                    '角色管理' =>
                        array (
                            'router' => 'backend::system.authorize.roles.index',
                            '创建' =>
                                array (
                                    'router' => 'backend::system.authorize.roles.create',
                                ),
                            '保存' =>
                                array (
                                    'router' => 'backend::system.authorize.roles.store',
                                ),
                            '编辑' =>
                                array (
                                    'router' => 'backend::system.authorize.roles.edit',
                                ),
                            '更新' =>
                                array (
                                    'router' => 'backend::system.authorize.roles.update',
                                ),
                            '删除' =>
                                array (
                                    'router' => 'backend::system.authorize.roles.destroy',
                                ),
                        ),
                ),
        ),
    '功能管理' =>
        array (
            'router' => 'backend::manage.index',
            '项目管理' =>
                array (
                    '管理项目' =>
                        array (
                            'router' => 'backend::manage.project.projects.index',
                            'backend::manage.project.projects.create' =>
                                array (
                                    'router' => 'backend::manage.project.projects.create',
                                ),
                            'backend::manage.project.projects.store' =>
                                array (
                                    'router' => 'backend::manage.project.projects.store',
                                ),
                            'backend::manage.project.projects.show' =>
                                array (
                                    'router' => 'backend::manage.project.projects.show',
                                ),
                            'backend::manage.project.projects.edit' =>
                                array (
                                    'router' => 'backend::manage.project.projects.edit',
                                ),
                            'backend::manage.project.projects.update' =>
                                array (
                                    'router' => 'backend::manage.project.projects.update',
                                ),
                            'backend::manage.project.projects.destroy' =>
                                array (
                                    'router' => 'backend::manage.project.projects.destroy',
                                ),
                            'backend::manage.project.projects.audit' =>
                                array (
                                    'router' => 'backend::manage.project.projects.audit',
                                ),
                            'backend::manage.project.projects.post-audit' =>
                                array (
                                    'router' => 'backend::manage.project.projects.post-audit',
                                ),
                            'backend::manage.project.projects.post-publish' =>
                                array (
                                    'router' => 'backend::manage.project.projects.post-publish',
                                ),
                            'backend::manage.project.projects.post-withdraw' =>
                                array (
                                    'router' => 'backend::manage.project.projects.post-withdraw',
                                ),
                            'backend::manage.project.projects.remark' =>
                                array (
                                    'router' => 'backend::manage.project.projects.remark',
                                ),
                            'backend::manage.project.projects.post-remark' =>
                                array (
                                    'router' => 'backend::manage.project.projects.post-remark',
                                ),
                            'backend::manage.project.projects.apply-index' =>
                                array (
                                    'router' => 'backend::manage.project.projects.apply-index',
                                ),
                            'backend::manage.project.projects.apply-show' =>
                                array (
                                    'router' => 'backend::manage.project.projects.apply-show',
                                ),
                            'backend::manage.project.projects.apply-update' =>
                                array (
                                    'router' => 'backend::manage.project.projects.apply-update',
                                ),
                        ),
                ),
            '开发者管理' =>
                array (
                    '管理开发者' =>
                        array (
                            'router' => 'backend::manage.developer.developers.index',
                            'backend::manage.developer.developers.show' =>
                                array (
                                    'router' => 'backend::manage.developer.developers.show',
                                ),
                            'backend::manage.developer.developers.audit' =>
                                array (
                                    'router' => 'backend::manage.developer.developers.audit',
                                ),
                            'backend::manage.developer.developers.post-audit' =>
                                array (
                                    'router' => 'backend::manage.developer.developers.post-audit',
                                ),
                        ),
                ),
        ),
);

