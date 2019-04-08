<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 创建用户表
 * Class CreateUsersTable
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('主键id');
            $table->string('name',20)->comment('用户昵称');
            $table->string('email')->comment('用户邮箱，可用于登录');
            $table->string('username',20)->comment('用户名，用于登录使用');
            $table->string('password')->comment('登录密码');
            $table->integer('avatar')->nullable()->comment('头像文件id');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('login_at')->nullable()->comment('本次登录时间');
            $table->ipAddress('login_ip')->nullable()->comment('本次登录IP地址');
            $table->timestamp('last_login_at')->nullable()->comment('上次登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('上次登录IP地址');
            $table->unique(['email','deleted_at'], 'users_email_unique');
            $table->unique(['name','deleted_at'], 'users_name_unique');
            $table->unique(['username','deleted_at'], 'users_username_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
