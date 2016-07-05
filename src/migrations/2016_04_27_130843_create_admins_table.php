<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * 创建众包管理员的管理员信息表
 * 管理员的角色另外建表
 *
 * Class CreateAdminsTable
 *
 * @author mzhu
 */
class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins',function(Blueprint $table){
            $table->increments('id');
            $table->string('name')->unique()->comment('管理员账户');
            $table->string('real_name')->comment('真实姓名');
            $table->string('email')->unique()->comment('管理员邮箱');
            $table->string('password')->comment('管理员密码');
            $table->string('editor_id')->default(0)->comment('修改者admin id');
            $table->timestamp('edited_at')->nullable()->comment('最后修改时间');
            $table->boolean('supervisor')->default(false)->comment('是否为超级管理员');
            $table->tinyInteger('status')->default(0)->comment('管理员账号状态');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admins');
    }
}
