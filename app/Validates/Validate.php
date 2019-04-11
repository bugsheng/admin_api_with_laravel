<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 12:50
 */

namespace App\Validates;


use App\Traits\BaseResponseTrait;
use Illuminate\Support\Facades\Validator;

class Validate
{
    use BaseResponseTrait;

    /**
     * @param $data
     * @param array $rules
     * @param array $message
     * @return bool|string
     */
    protected function validate($data, $rules = [], $message = []){

        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }

}
