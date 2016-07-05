<?php

namespace Xiaoshu\Admin\Models;

use Xiaoshu\Foundation\Model;
use Xiaoshu\Admin\Services\System\AdminNodeService;

class BackendRole extends Model
{
    protected $table = 'backend_roles';

    protected $fillable = [
        'title',
        'nodes',
    ];

    /*------------------------------
     * relations
     -----------------------------*/

    public function editor()
    {
        return $this->belongsTo(Admin::class,'editor_id','id');
    }

    /*------------------------------
     * extends
     -----------------------------*/

    public function getNodeTree()
    {
        return $this->getNodeService()->buildRoleAuthorsTree($this);
    }

    /**
     * @return AdminNodeService
     */
    public function getNodeService()
    {
        return $this->make(AdminNodeService::class);
    }

    /*------------------------------
     * mutator
     -----------------------------*/

    public function getNodesAttribute($value)
    {
        return static::fieldToArray($value);
    }

    public function setNodesAttribute( $value)
    {
        $this->attributes['nodes'] = static::arrayToField($value);
    }

    public function getEditorNameAttribute()
    {
        $editor = $this->editor;
        return $editor ? $editor->name : '无';
    }

}
