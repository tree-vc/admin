<?php
/**
 * Date: 16/5/17
 * Time: 下午4:21
 */

namespace Xiaoshu\Admin\Services\Logic;

use Xiaoshu\Foundation\Logic;
use Xiaoshu\Foundation\Result\Result;
use Xiaoshu\Foundation\Util\Option;
use Xiaoshu\Admin\Models\BackendRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use Exception;

/**
 * Class AdminRoleService
 * @package App\Services\Logic
 * @author zhuming
 */
class BackendRoleService extends Logic
{

    public function findRoleOrFail($id)
    {
        return BackendRole::findOrFail($id);
    }

    /**
     * @param int $id
     * @param Authenticatable $editor
     * @param array $data
     * @return Result
     */
    public function updateRoleOrFail($id , Authenticatable $editor , $data)
    {
        $role = BackendRole::findOrFail($id);

        $keys = ['title','nodes'];
        $data = Option::filter($keys,$data);

        try{
            static::beginTransaction();
            $role->editor_id = $editor->id;
            $role->edited_at = date('Y-m-d H:i:s');
            $role->update($data);
            static::commit();
            return Result::success('更新成功');
        } catch(Exception $e) {
            static::rollback();
            $this->logError(__METHOD__,$e);
            return Result::fail('更新失败');
        }

    }

    public function paginateRoles($page , $orderType = 'desc' , $rows = 10 , $pageName = 'page',$fields = ['*'])
    {
        $orderType = in_array($orderType,['asc','desc']) ? $orderType : 'desc';
        $roles = BackendRole::with('editor')
            ->orderBy('updated_at',$orderType)
            ->orderBy('created_at',$orderType)
            ->paginate($rows,$fields,$pageName,$page);
        return $roles;
    }

    /**
     * @param Authenticatable $editor
     * @param $title
     * @param array $nodes
     * @return Result
     */
    public function createRole(Authenticatable $editor , $title , array $nodes = [])
    {
        try {
            static::beginTransaction();

            $role = new BackendRole([
                'title' =>  $title,
                'nodes' =>  $nodes,
            ]);

            $role->editor_id = $editor->id;
            $role->edited_at = date('Y-m-d H:i:s');
            $role->save();

            static::commit();
            return Result::success('创建角色成功');
        } catch (\Exception $e) {
            static::rollback();
            $this->logError(__METHOD__,$e);
            return Result::fail('创建角色失败');
        }

    }

    public function deleteRole(Authenticatable $editor , $id)
    {
        try {
            $role = BackendRole::find($id);
            if(!$role) {
                return Result::fail('角色不存在');
            }

            static::beginTransaction();
            $role->delete();
            static::commit();

            return Result::success('成功删除角色');
        } catch(\Exception $e ) {
            $this->logError(__METHOD__,$e);
            return Result::fail('删除角色失败');
        }

    }

    public function getAllRolesArray()
    {
        $roles = BackendRole::orderBy('id','asc')->get(['id','title']);

        return array_reduce($roles->all(),function($result , $role){
            $result[$role->id] = $role->title;
            return $result;
        },[ 0 => '系统管理员']);
    }

}