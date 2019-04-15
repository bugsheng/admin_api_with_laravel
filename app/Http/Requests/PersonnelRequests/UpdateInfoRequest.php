<?php

namespace App\Http\Requests\PersonnelRequests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 修改个人信息请求验证
 * Class UpdateCurrentUserInfoRequest
 * @package App\Http\Requests
 */
class UpdateInfoRequest extends FormRequest
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
            'name'      => 'required',
            'email'     => 'bail|required|email',
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
            'name.required'     => '昵称不可为空',
            'email.required'    => '个人邮箱不可为空',
            'email.email'       => '邮箱格式不正确',
        ];
    }
}
