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
            $table->string('name','20')->comment('用户昵称');
            $table->string('email')->unique()->comment('用户邮箱，可用于登录、找回密码、接收系统推送邮件');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('username','20')->unique()->comment('用户名，用于登录使用');
            $table->string('password');
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
        Schema::dropIfExists('users');
    }
}
