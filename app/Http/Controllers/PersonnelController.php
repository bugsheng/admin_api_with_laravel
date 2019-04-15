<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/9
 * Time: 16:02
 */

namespace App\Http\Controllers;


use App\Http\Requests\PersonnelRequests\UpdateInfoRequest;
use App\Http\Requests\PersonnelRequests\UpdateLoginPasswordRequest;
use App\Services\Interfaces\PersonnelInterface as PersonnelService;

/**
 * 用户个人信息操作相关
 * Class CurrentUserController
 * @package App\Http\Controllers
 */
class PersonnelController extends Controller
{

    /**
     * @var PersonnelService
     */
    protected $personnelService;

    /**
     * CurrentUserController constructor.
     * @param PersonnelService $personnelService
     */
    public function __construct(PersonnelService $personnelService)
    {
        $this->personnelService = $personnelService;
    }

    /**
     * 登录后获取用户基础信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(){

        $result = $this->personnelService->getUserInfo();

        $data = [
            'personnel' => $result['data']
        ];

        return $this->success($data);

    }

    //获取用户权限
    public function getPermissions(){}

    /**
     * 更新用户基础信息
     * @param UpdateInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(UpdateInfoRequest $request){

        //获取有效参数
        $require_data = $request->only(['name','email']);

        //更新信息
        $result = $this->personnelService->updateInfo($require_data);

        if(!$result['status']){
            return $this->failed($result['message']);
        }

        return $this->message('更新信息操作成功');

    }

    /**
     * 修改用户密码
     * @param UpdateLoginPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdateLoginPasswordRequest $request){

        //获取有效参数
        $require_data = $request->only(['old_password','password']);

        //更新密码
        $result = $this->personnelService->updatePassword($require_data['old_password'],$require_data['password']);

        if(!$result['status']){
            return $this->failed($result['message']);
        }

        return $this->message('修改登录密码操作成功');
    }

    //修改用户头像
    public function updateAvatar(){}

}
