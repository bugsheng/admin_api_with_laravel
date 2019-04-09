<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/9
 * Time: 16:02
 */

namespace App\Http\Controllers;


use App\Services\CurrentUserService;
use Illuminate\Http\Request;

/**
 * 用户个人信息操作相关
 * Class CurrentUserController
 * @package App\Http\Controllers
 */
class CurrentUserController extends Controller
{

    /**
     * @var CurrentUserService
     */
    protected $currentUserService;

    /**
     * CurrentUserController constructor.
     * @param CurrentUserService $currentUserService
     */
    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    /**
     * 登录后获取用户基础信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(){

        $result = $this->currentUserService->getUserInfo();

        $data = [
            'currentUser' => $result['data']
        ];

        return $this->success($data);

    }

    //获取用户权限
    public function getPermissions(){}

    //更新用户基础信息
    public function updateInfo(Request $request){

        $require_data = $request->only(['name','email']);

        $result = $this->currentUserService->updateInfo($require_data);

        if(!$result['status']){
            return $this->failed($result['message']);
        }

        return $this->message('更新用户信息操作成功');

    }

    //修改用户密码
    public function updatePassword(){}

    //修改用户头像
    public function updateAvatar(){}

}
