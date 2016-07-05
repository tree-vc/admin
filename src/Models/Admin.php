<?php

namespace Xiaoshu\Admin\Models;

use Xiaoshu\Foundation\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanRestPasswordContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Cache;

class Admin extends Model implements
    AuthenticatableContract,
    CanRestPasswordContract,
    AuthorizableContract
{
    use Authenticatable,Authorizable,CanResetPassword;

    const STATUS_NORMAL = 0;
    const STATUS_LOCK   = 30;
    const STATUS_DELETE = 40;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'real_name',
        'email',
        'password',
    ];

    protected $hidden = ['password','remember_token','supervisor'];

    protected $isInSupervisor;

    /*------------------------------
     * static
     -----------------------------*/

    public static function getStatusTexts()
    {
        return [
            static::STATUS_NORMAL   =>  '正常',
            static::STATUS_LOCK     =>  '锁定',
            static::STATUS_DELETE   =>  '删除',
        ];
    }

    public static function getStatusArr()
    {
        return [
            'normal'    =>  static::STATUS_NORMAL,
            'lock'      =>  static::STATUS_LOCK,
            'delete'    =>  static::STATUS_DELETE,
        ];
    }

    public static function getStatusToTexts()
    {
        return [
            'normal'    =>  '正常',
            'lock'      =>  '锁定',
            'delete'    =>  '删除',
        ];
    }


    /*------------------------------
     * extends
     -----------------------------*/

    /**
     * @return bool
     */
    public function isSupervisor()
    {
        if($this->isDisabled()){
            return false;
        }

        if(is_bool($this->isInSupervisor)){
            return $this->isInSupervisor;
        }

        return $this->isInSupervisor = in_array($this->attributes['name'] , $this->getSupervisors() );
    }

    protected function getSupervisors()
    {
        return config('backend.supervisors');
    }

    public function isDisabled()
    {
        return $this->attributes['status'] !== static::STATUS_NORMAL;
    }

    public function isLocked()
    {
        return $this->attributes['status'] === static::STATUS_LOCK ;
    }

    public function isDeleted()
    {
        return $this->attributes['status'] === static::STATUS_DELETE;
    }

    public function setStatus($status)
    {
        $statusArr = static::getStatusArr();

        if(!isset($statusArr[$status])){
            return false;
        }

        $this->attributes['status'] = $statusArr[$status];
        return true;
    }


    /*------------------------------
     * scope
     -----------------------------*/

    public function scopeStatusOf($query,$status = null)
    {
        if(!$status || $status === 'all'){
            return $query;
        }

        $statusArr = [
            'normal'    =>  static::STATUS_NORMAL,
            'lock'      =>  static::STATUS_LOCK,
            'delete'    =>  static::STATUS_DELETE,
        ];

        if(isset($statusArr[$status])){
            return $query->where('status',$statusArr[$status]);
        }

        return $query;
    }

    /*------------------------------
     * relations
     -----------------------------*/


    /**
     * 获取当前账号的角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongstoMany(BackendRole::class,'admin_roles','admin_id','role_id');
    }

    public function adminRoles()
    {
        return $this->hasMany(AdminRole::class,'admin_id','id');
    }

    public function editor()
    {
        return $this->belongsTo(__CLASS__,'editor_id','id');
    }


    /*------------------------------
     * mutator
     -----------------------------*/

    public function getAuthorsAttribute()
    {
        $roles      = $this->roles ? : [];
        $authors    = array_reduce($roles->all() ,function($authors , $role){
            return array_unique(array_merge($authors , $role->nodes));
        },[]);
        return $authors;
    }

    public function getStatusTextAttribute()
    {
        $status = $this->attributes['status'];
        $texts  = static::getStatusTexts();
        return isset($texts[$status]) ? $texts[$status] : '';
    }

    public function getRolesArrAttribute()
    {
        $roles = [];
        if($this->isSupervisor()){
            $roles[0] = '系统管理员';
        }

        return array_reduce($this->roles->all(),function($roles,$role){
            $roles[$role->id] = $role->title;
            return $roles;
        },$roles) ? : [];
    }

    public function getRolesTextAttribute()
    {
        $roles = $this->getRolesArrAttribute();
        $str   = implode(',', (array)$roles);
        return $str? : '';
    }

    public function getEditorNameAttribute()
    {
        $editor = $this->editor;
        return  $editor ? $editor->name : '无';
    }
}
