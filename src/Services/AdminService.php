<?php
/**
 * Date: 16/5/23
 * Time: 上午11:59
 */

namespace Xiaoshu\Admin\Services;

use Xiaoshu\Foundation\Logic;
use Xiaoshu\Foundation\Result\Result;
use Xiaoshu\Foundation\Util\Option;
use Xiaoshu\Admin\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Mail;

class AdminService extends Logic
{

    public function getStatusTexts()
    {
        return Admin::getStatusTexts();
    }

    public function getStatusOptions()
    {
        return [
            'normal'    =>  '正常',
            'lock'      =>  '锁定',
            'delete'    =>  '删除',
        ];
    }

    public function adminExists($id)
    {
        return Admin::where('id',$id)->count();
    }

    public function findOrFail($id)
    {
        return Admin::findOrFail($id);
    }

    public function listAdmins($page,$status = null ,array $orders = [],$lines = null, $fields = ['*'])
    {
        $status  = in_array($status , ['all','normal','lock','delete']) ? $status : 'all';

        $builder = Admin::statusOf($status);

        foreach($orders as $name => $order){
            if(!in_array($order,['asc','desc'])){
                continue;
            }
            var_dump($name.$order);
            $builder->orderBy($name , $order);
        }
        $builder->with('roles','editor');

        $admins  = $builder->paginate($lines,$fields,'page',$page);

        return $admins;
    }

    /**
     * @param Authenticatable $editor
     * @param array $data
     * @param array $roles
     * @return Result
     */
    public function createAdmin(Authenticatable $editor , $data , $roles = [])
    {
        $keys = [
            'name',
            'real_name',
            'email',
        ];

        $mass   = Option::filter($keys , $data);

        try {

            static::beginTransaction();
            $newAdmin = new Admin($mass);
            $newAdmin->password  = bcrypt($data['password']);
            $newAdmin->editor_id = $editor->id;
            $newAdmin->save();

            if($roles){
                $newAdmin->roles()->sync($roles);
            }

            static::commit();
            return Result::success('创建管理员成功')->set('id',$newAdmin->id);

        } catch (\Exception $e ){
            static::rollback();
            $this->logError(__METHOD__,$e);
            return Result::fail('创建管理员失败');
        }
    }


    /**
     * @param Authenticatable $editor
     * @param int $id
     * @param null|string $email
     * @param null|string $password
     * @param null|int $status
     * @param array $roles
     * @return Result
     */
    public function updateAdmin(Authenticatable $editor , $id , $email = null ,$password = null , $status = null , $roles = [])
    {
        $admin = Admin::findOrFail($id);

        try {
            static::beginTransaction();
            if($email){
                $admin->email = $email;
            }
            if($password){
                $admin->password = bcrypt($password);
            }

            if($roles){
                $admin->roles()->sync($roles);
            }

            $statusTexts = Admin::getStatusTexts();

            if(isset($statusTexts[$status])){
                $admin->status = $status;
            }

            $admin->editor_id = $editor->id;
            $admin->edited_at = date('Y-m-d H:i:s');

            $admin->save();
            static::commit();
            return Result::success('更新管理员成功');
        } catch(\Exception $e){
            static::rollback();
            $this->logError(__METHOD__ , $e);
            return Result::fail('更新管理员失败');
        }

    }

    /**
     * @param Authenticatable $editor
     * @param int $id
     * @param bool $soft
     * @return Result
     */
    public function softDeleteAdmin(Authenticatable $editor , $id , $soft = true)
    {
        $admin = Admin::findOrFail($id);

        if($admin->isDeleted()){
            return Result::fail('管理员已删除');
        }

        try {
            static::beginTransaction();

            if($soft){
                $admin->setStatus('delete');
                $admin->editor_id = $editor->id;
                $admin->edited_at = date("Y-m-d H:i:s");
                $admin->save();
            } else {
                $admin->delete();
            }
            static::commit();

            return Result::success('删除管理员成功');
        } catch(\Exception $e){
            static::rollback();
            $this->logError(__METHOD__ , $e);

            return Result::fail('删除管理员失败');
        }

    }

    /**
     * @param boolean $status
     * @param Authenticatable $editor
     * @param int $id
     * @return Result
     */
    public function setAdminLock($status = true, Authenticatable $editor , $id)
    {
        $admin = Admin::findOrFail($id);

        $statusCode = $status ? 'lock' : 'normal' ;

        if($admin->isDeleted()){
            return Result::fail('管理员已删除,不能修改');
        }

        try {
            static::beginTransaction();

            $admin->setStatus($statusCode);
            $admin->editor_id = $editor->id;
            $admin->edited_at = date("Y-m-d H:i:s");
            $admin->save();

            static::commit();
            return Result::success('修改管理员状态成功');
        } catch(\Exception $e){
            static::rollback();
            $this->logError(__METHOD__ , $e);

            return Result::fail('修改管理员状态失败');
        }
    }

    public function resetPassword($name, $email)
    {
        $admin = Admin::where(['name' => $name, 'email' => $email])->first();
        if (empty($admin)){
            return Result::fail('用户名或邮箱错误，请检查');
        }

        $newPassword = rand(100000 , 999999);

        try {
            static::beginTransaction();

            $admin->password = bcrypt($newPassword);
            $admin->save();

            static::commit();

            $data = ['email' => $admin->email, 'name' => $admin->name, 'newPassword' => $newPassword];
            Mail::send('backend.auth.reset-password-email', $data, function($message) use($data)
            {
                $message->to($data['email'], $data['name'])->subject('密码重置');
            });

            $result = Result::success('提交成功');
        } catch(\Exception $e){
            static::rollback();
            $this->logError(__METHOD__ , $e);

            $result = Result::fail('提交失败');
        }

        return $result;
    }
}