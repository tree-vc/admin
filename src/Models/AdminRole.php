<?php

namespace Xiaoshu\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use Xiaoshu\Admin\Models\BackendRole;

class AdminRole extends Model
{
    protected $table = 'admin_roles';
    //

    public function backendRole()
    {
        return $this->belongsTo(BackendRole::class,'role_id','id');
    }
}
