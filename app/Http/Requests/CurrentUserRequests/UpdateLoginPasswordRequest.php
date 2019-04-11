<?php

namespace App\Http\Requests\CurrentUserRequests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 更新用户密码请求验证
 * Class UpdateLoginPasswordRequest
 * @package App\Http\Requests\CurrentUserRequests
 */
class UpdateLoginPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password'          => 'required',
            'password'              => 'bail|required|different:old_password|min:6|confirmed',
            'password_confirmation' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'old_password.required'             => '请输入原密码',
            'password.required'                 => '请输入新密码',
            'password.different'                => '新密码与旧密码不可相同',
            'password.min'                      => '密码不可少于6位',
            'password.confirmed'                => '两次密码输入不一致',
            'password_confirmation.required'    => '请输入确认密码'
        ];
    }
}
