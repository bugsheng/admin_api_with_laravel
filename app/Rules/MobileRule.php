<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 14:24
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

/**
 * 手机号码验证规则
 * Class MobileRule
 * @package App\Rules
 */
class MobileRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return isMobile($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '手机号码不正确';
    }
}

