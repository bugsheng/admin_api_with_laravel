<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 17:43
 */

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // 生成数据集合
        factory(\App\Models\User::class, 5)->create();

        // 单独处理第一个用户的数据
        $user = \App\Models\User::find(1);
        $user->name = 'admin';
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('123456');
        $user->save();


    }
}
