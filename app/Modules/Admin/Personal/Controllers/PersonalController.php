<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/9
 * Time: 16:02
 */

namespace App\Modules\Admin\Personal\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Admin\Personal\Requests\UpdateInfoRequest;
use App\Modules\Admin\Personal\Requests\UpdateLoginPasswordRequest;
use App\Modules\Admin\Personal\Services\PersonalService;

/**
 * 用户个人信息操作相关
 * Class CurrentUserController
 * @package App\Http\Controllers
 */
class PersonalController extends Controller
{

    /**
     * @var PersonalService
     */
    protected $personalService;

    /**
     * CurrentUserController constructor.
     * @param PersonalService $personalService
     */
    public function __construct(PersonalService $personalService)
    {
        $this->personalService = $personalService;
    }

    /**
     * 登录后获取用户基础信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(){

        $infoResult = $this->personalService->getInfo();

        $result = [
            'info' => $infoResult['data']
        ];

        return $this->success($result);

    }

    /*管理用户拥有权限的后台管理菜单*/
    public function menus(){

    }

    /*管理用户拥有的操作接口权限*/
    public function permissions(){

    }

    /**
     * 更新用户基础信息
     * @param UpdateInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(UpdateInfoRequest $request){

        //获取有效参数
        $require_data = $request->only(['name','email']);

        //更新信息
        $result = $this->personalService->updateInfo($require_data);

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
        $result = $this->personalService->updatePassword($require_data['old_password'],$require_data['password']);

        if(!$result['status']){
            return $this->failed($result['message']);
        }

        return $this->message('修改登录密码操作成功');
    }

    //修改用户头像
    public function updateAvatar(){}

}
