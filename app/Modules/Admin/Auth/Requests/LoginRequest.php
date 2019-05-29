<?php

namespace App\Modules\Admin\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 登录请求表单验证
 * Class LoginRequest
 * @package App\Http\Requests
 */
class LoginRequest extends FormRequest
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
            'username'  => 'required',
            'password'  => 'required|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => '请输入用户名',
            'password.required' => '请输入密码',
            'password.min'      => '密码长度至少是6位',
        ];
    }
}
