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
 * 汉字姓名验证规则
 * Class ChineseRule
 *
 * @package App\Rules
 */
class ChineseRule implements Rule
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
        return isAllChinese($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '请输入正确的姓名(中文)';
    }
}
