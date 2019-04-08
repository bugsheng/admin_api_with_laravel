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
        $users = factory(\App\Models\User::class)
            ->times(5)
            ->make();

        // 让隐藏字段可见，并将数据集合转换为数组
        $user_array = $users->makeVisible(['password', 'remember_token'])->toArray();

        // 插入到数据库中
        \App\Models\User::insert($user_array);

        // 单独处理第一个用户的数据
        $user = \App\Models\User::find(1);
        $user->name = 'admin';
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('123456');
        $user->save();

    }
}
