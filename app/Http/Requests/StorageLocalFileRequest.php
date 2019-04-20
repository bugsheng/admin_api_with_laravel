<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorageLocalFileRequest extends FormRequest
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
        $rules = [
            'file' =>[ 'required']
        ];

        //如果是多个文件上传
        if(is_array($this->file('file'))){
            $rules['file.*'] = ['file'];
        }else{
            $rules['file'][] = 'file';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'file.required' => '请上传文件',
            'file.file'     => '请上传文件',
            'file.*.file'   => '请上传文件',
        ];
    }
}
