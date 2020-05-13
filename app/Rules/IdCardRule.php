<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 14:18
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 身份证号码验证规则
 * Class IdCardRule
 *
 * @package App\Rules
 */
class IdCardRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return isIDCard($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '请输入正确的身份证号码';
    }
}
