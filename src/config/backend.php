<?php
/**
 * Date: 16/5/24
 * Time: 下午2:42
 */
return [

    'supervisors'    =>  [
        'admin',
    ],

    'except_admin_routes' => [
        'backend::auth.login',
        'backend::auth.post-login',
        'backend::auth.reset-password',
        'backend::auth.post-reset-password',
        'backend::auth.logout',
        'backend::index',
    ],

];