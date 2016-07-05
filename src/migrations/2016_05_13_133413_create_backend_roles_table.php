<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateBackendRolesTable
 * @author zhuming
 */
class CreateBackendRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backend_roles',function(Blueprint $table){
            $table->increments('id');
            $table->string('title')->unique();
            $table->text('nodes');
            $table->integer('editor_id')->default(0)->comment('编辑者');
            $table->timestamp('edited_at')->nullable()->commment('编辑时间');
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
        Schema::dropIfExists('backend_roles');
    }
}
