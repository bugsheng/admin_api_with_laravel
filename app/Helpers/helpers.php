<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 14:15
 */

if (!function_exists('arrayGroup')) {
    /**
     * 将二维数组根据某一个key进行分组重组
     *
     * @param $array
     * @param $group_key
     *
     * @return array
     */
    function arrayGroup($array, $group_key)
    {

        if (!$array) {
            return [];
        }

        $isStdClass = false;
        if (!is_array($array[0])) {
            $isStdClass = true;
        }

        $cur_arr = [];   //current row
        $result  = [];
        foreach ($array as $item) {
            if ($isStdClass) {
                $cur_arr = (array)$item;
            } else {
                $cur_arr = $item;
            }

            if (!array_key_exists($group_key, $cur_arr)) {
                return [];
            }

            $result[$cur_arr[$group_key]][] = $cur_arr;

        }

        unset($cur_arr);

        return $result;

    }
}

if (!function_exists('generateSMSCode')) {
    /**
     * 生成指定长度的数字验证码
     *
     * @param $len
     *
     * @return bool|string
     */
    function generateSMSCode($len)
    {

        $len = intval($len);
        if ($len === 0) {
            return false;
        }

        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code = $code . rand(0, 9);
        }

        return $code == '' ? false : $code;
    }
}

if (!function_exists('deepInArray')) {
    /**
     * 判断一个多维数组中是否存在某一个值
     *
     * @param $value
     * @param $array
     *
     * @return bool
     */
    function deepInArray($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if (in_array($value, $item)) {
                return true;
            } else {
                if (deepInArray($value, $item)) {
                    return true;
                }
            }
        }
        return false;
    }
}

if (!function_exists('isJson')) {
    /**
     * 判断一个字符串是否是有效的json字符串
     *
     * @param $string
     *
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('isMobile')) {
    /**
     * 验证手机号码是否正确
     *
     * @param String   $mobile    手机号码
     * @param bool|int $is_strict 是否严格模式
     *
     * @return boolean
     */
    function isMobile($mobile, $is_strict = false)
    {
        //手机号码验证规则
        if ($is_strict) {
            $regx = "/^((1[3,4,5,7,8][0-9])|(14[5,6,7,8,9])|(16[6])|(19[9]))\d{8}$/";
        } else {
            $regx = "/^\d{11}$/";
        }

        if (preg_match($regx, $mobile)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('isAllChinese')) {
    /**
     * 判断姓名是否全是中文
     *
     * @param $str
     *
     * @return bool
     */
    function isAllChinese($str)
    {
        //新疆等少数民族可能有·
        if (strpos($str, '·')) {
            //将·去掉，看看剩下的是不是都是中文
            $str = str_replace("·", '', $str);
            if (preg_match('/^[\x7f-\xff]+$/', $str)) {
                return true;//全是中文
            } else {
                return false;//不全是中文
            }
        } else {
            if (preg_match('/^[\x7f-\xff]+$/', $str)) {
                return true;//全是中文
            } else {
                return false;//不全是中文
            }
        }
    }
}

if (!function_exists('isIDCard')) {
    /**
     * 验证身份证号码是否正确
     *
     * @param String   $id        身份证号码
     * @param bool|int $is_strict 是否严格模式
     *
     * @return boolean
     */
    function isIDCard($id = '', $is_strict = false)
    {
        $id        = strtoupper($id);
        $regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = [];
        if (!preg_match($regx, $id)) {
            return false;
        }

        if (!$is_strict) {
            return true;
        }

        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return false;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                $arr_ch  = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
                $sign    = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b    = (int)$id{$i};
                    $w    = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n       = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return false;
                } //phpfensi.com
                else {
                    return true;
                }
            }
        }

    }
}

if (!function_exists('numberToChinese')) {
    /**
     *　数字金额转换成中文大写金额的函数
     *　@param int $num 要转换的小写数字或小写字符串
     *　@return string 大写文字
     *　小数位为两位
     **/
    function numberToChinese($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角圆拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '圆'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j    = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零圆' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left  = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c     = $left . $right;
                $j     = $j - 3;
                $slen  = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }
}

if (!function_exists('chineseToNumber')) {
    /**
     * 中文转换成阿拉伯数字
     *
     * @param $string
     *
     * @return float|int|mixed
     */
    function chineseToNumber($string)
    {
        if (is_numeric($string)) {
            return $string;
        }
        // '仟' => '千','佰' => '百','拾' => '十',
        $string = str_replace('仟', '千', $string);
        $string = str_replace('佰', '百', $string);
        $string = str_replace('拾', '十', $string);
        $num    = 0;
        $wan    = explode('万', $string);
        if (count($wan) > 1) {
            $num    += chineseToNumber($wan[0]) * 10000;
            $string = $wan[1];
        }
        $qian = explode('千', $string);
        if (count($qian) > 1) {
            $num    += chineseToNumber($qian[0]) * 1000;
            $string = $qian[1];
        }
        $bai = explode('百', $string);
        if (count($bai) > 1) {
            $num    += chineseToNumber($bai[0]) * 100;
            $string = $bai[1];
        }
        $shi = explode('十', $string);
        if (count($shi) > 1) {
            $num    += chineseToNumber($shi[0] ? $shi[0] : '一') * 10;
            $string = $shi[1] ? $shi[1] : '零';
        }
        $ling = explode('零', $string);
        if (count($ling) > 1) {
            $string = $ling[1];
        }
        $d = [
            '一' => '1',
            '二' => '2',
            '三' => '3',
            '四' => '4',
            '五' => '5',
            '六' => '6',
            '七' => '7',
            '八' => '8',
            '九' => '9',
            '壹' => '1',
            '贰' => '2',
            '叁' => '3',
            '肆' => '4',
            '伍' => '5',
            '陆' => '6',
            '柒' => '7',
            '捌' => '8',
            '玖' => '9',
            '零' => 0,
            '0' => 0,
            'O' => 0,
            'o' => 0,
            '两' => 2
        ];
        return $num + @$d[$string];
    }
}

if (!function_exists('loadImg')) {
    /**
     * 保存网络图片到服务器
     * 小程序传的头像是网络地址需要周转一下
     *
     * @param $image_url
     * @param $local_url
     *
     * @return bool|int
     */
    function loadImg($image_url, $local_url)
    {
        $img_file    = file_get_contents($image_url);
        $img_content = base64_encode($img_file);
        $result      = file_put_contents($local_url, base64_decode($img_content));

        return $result;
    }
}

if (!function_exists('getClientIp')) {
    /**
     * 获取客户端 ip
     *
     * @return array|false|null|string
     */
    function getClientIp()
    {
        static $realip = null;
        if ($realip !== null) {
            return $realip;
        }
        //判断服务器是否允许$_SERVER
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            //不允许就使用getenv获取
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }
}

if (!function_exists('issetAndNotEmpty')) {
    /**
     * 判断数组的键是否存在，并且佱不为空
     *
     * @param $arr
     * @param $column
     *
     * @return null
     */
    function issetAndNotEmpty($arr, $column)
    {
        return (isset($arr[$column]) && $arr[$column]) ? $arr[$column] : '';
    }
}

if (!function_exists('trimAllBlankSpace')) {
    /**
     * 过滤用户输入数据中的空格 全角空格 tab
     *
     * @param $str
     *
     * @return mixed
     *
     */
    function trimAllBlankSpace($str)
    {
        $search  = [" ", "　", "\t"];
        $replace = ["", "", ""];
        return str_replace($search, $replace, $str);
    }
}

if (!function_exists('getHourAndMin')) {
    /**
     * 将时间戳转换成 xx 时\xx 分
     *
     * @param $time
     *
     * @return array
     */
    function getHourAndMin($time)
    {
        $sec = round($time / 60);
        if ($sec >= 60) {
            $hour = floor($sec / 60);
            $min  = $sec % 60;

        } else {
            $hour = 0;
            $min  = $sec;
        }
        return ['hour' => $hour, 'min' => $min];
    }
}

if (!function_exists('getTowPositionDistance')) {
    /**
     * 根据经纬度获取两点间的直线距离，返回 KM
     *
     * @param $lon1
     * @param $lat1
     * @param $lon2
     * @param $lat2
     *
     * @return float
     */
    function getTowPositionDistance($lon1, $lat1, $lon2, $lat2)
    {
        $radius = 6378.137;
        $rad    = floatval(M_PI / 180.0);

        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;

        $theta = $lon2 - $lon1;

        $dist = acos(sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($theta)
        );

        if ($dist < 0) {
            $dist += M_PI;
        }

        return round($dist * $radius, 3);
    }
}

if (!function_exists('get_order_sn')) {
    /**
     * 生成唯一订单号
     *
     * @param string $pre        前缀
     * @param string $table_name 存放订单数据库名称
     * @param string $column     订单号字段名
     *
     * @return string 订单号
     */
    function get_order_sn($pre = '', $table_name = '', $column = 'order_sn')
    {
        mt_srand((double)microtime() * 1000000);

        $str = $pre . date('Ymd') . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);;
        if ($table_name && $column) {
            $sn = \Illuminate\Support\Facades\DB::table($table_name)->where($column, $str)->count();
            if ($sn > 0) {
                get_order_sn($pre, $table_name, $column);
            }
        }
        return $str;
    }
}

if (!function_exists('http_post_no_rest')) {
    /**
     * 发起一个http 请求
     *
     * @param $url
     * @param $data
     *
     * @return bool|string
     */
    function http_post_no_rest($url, $data)
    {
        $post_data = http_build_query(
            $data
        );

        $opts    = [
            'http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
                ]
        ];
        $context = stream_context_create($opts);
        $result  = file_get_contents($url, false, $context);
        return $result;
    }
}

if (!function_exists('http_post_request')) {
    /**
     * http post请求
     *
     * @param       $url
     * @param array $params
     *
     * @return mixed
     */
    function http_post_request($url, array $params)
    {
        $params  = json_encode($params, JSON_FORCE_OBJECT);
        $headers = [
            "Content-Type:application/json;charset=utf-8",
            "Accept:application/json;charset=utf-8"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('http_get_request')) {
    /**
     * curl一个http get请求
     *
     * @param $url
     * @param $params
     *
     * @return mixed
     */
    function http_get_request($url, string $params)
    {
        $url = $url . '?' . http_build_query($params);
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

if (!function_exists('addslashes_deep')) {
    /**
     * 递归方式的对变量中的特殊字符进行转义
     *
     * @param $value
     *
     * @return array|string
     */
    function addslashes_deep($value)
    {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
        }
    }
}

if (!function_exists('user_text_encode')) {
    /**
     * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
     *
     * @param $str
     *
     * @return mixed|string
     */
    function user_text_encode($str)
    {
        if (!is_string($str)) {
            return $str;
        }
        if (!$str || $str == 'undefined') {
            return '';
        }

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, $text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }
}

if (!function_exists('user_text_decode')) {
    /**
     * 解码userTextEncode转义的内容 与 userTextEncode配对使用
     *
     * @param $str
     *
     * @return mixed
     */
    function user_text_decode($str)
    {
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, $text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }
}

if (!function_exists('get_initials')) {
    /**
     * 获取首字母
     *
     * @param  string $str 汉字字符串
     *
     * @return string 首字母
     */
    function get_initials($str)
    {
        if (empty($str)) {
            return '#';
        }
        $fChar = ord($str{0});
        if ($fChar >= ord('A') && $fChar <= ord('z')) {
            return strtoupper($str{0});
        }

        $s1  = iconv('UTF-8', 'gb2312', $str);
        $s2  = iconv('gb2312', 'UTF-8', $s1);
        $s   = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }


        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }


        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }


        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }


        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }


        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }


        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }


        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }


        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }


        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }


        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }


        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }


        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }


        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }


        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }


        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }


        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }


        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }


        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }


        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }


        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }


        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }


        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }


        return '#';
    }
}

if (!function_exists('get_array_repeat')) {
    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值
     *
     * @param array $arr  目标数组
     * @param array $keys 要进行判断的键值组合的数组
     *
     * @return array 重复的值
     */
    function get_array_repeat($arr = [], $keys = [])
    {
        $unique_arr = [];
        $repeat_arr = [];
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if (!in_array($str, $unique_arr)) {
                $unique_arr[] = $str;
            } else {
                $repeat_arr[] = $v;
            }
        }
        return $repeat_arr;
    }
}

if (!function_exists('has_array_repeat')) {
    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值
     *
     * @param array $arr  目标数组
     * @param array $keys 要进行判断的键值组合的数组
     *
     * @return bool 是有重复 true有重复 false无重复
     */
    function has_array_repeat($arr = [], $keys = [])
    {
        $unique_arr = [];
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if (!in_array($str, $unique_arr)) {
                $unique_arr[] = $str;
            } else {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('is_assoc')) {
    /**
     * 作用：检测数组是否为索引数组。
     *
     * @param array $arr 传入的数组
     *
     * @return bool
     */
    function is_assoc(array $arr)
    {
//        return array_keys($arr) !== range(0, count($arr) - 1);
        return (bool)count(array_filter(array_keys($arr), 'is_string'));
    }
}

if (!function_exists('str_replace_limit')) {
    /**
     * 对字符串执行指定次数替换
     *
     * @param Mixed $search  查找目标值
     * @param Mixed $replace 替换值
     * @param Mixed $subject 执行替换的字符串／数组
     * @param Int   $limit   允许替换的次数，默认为-1，不限次数
     *
     * @return Mixed
     */
    function str_replace_limit($search, $replace, $subject, $limit = -1)
    {
        if (is_array($search)) {
            foreach ($search as $k => $v) {
                $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
            }
        } else {
            $search = '`' . preg_quote($search, '`') . '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }
}

if (!function_exists('fun_adm_each')) {
    /**
     * php7.2废弃each方法，该方法为each的替代方法
     *
     * @param $array
     *
     * @return array|bool
     */
    function fun_adm_each(&$array)
    {
        $res = [];
        $key = key($array);
        if ($key !== null) {
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        } else {
            $res = false;
        }
        return $res;
    }

}

if (!function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree
     *
     * @param array  $list 要转换的数据集
     * @param string $pid  parent标记字段
     *
     * @return array
     */
    function list_to_tree($list, $pid = 'parent_id')
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data['id']] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($parentId == 0) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent               =& $refer[$parentId];
                        $parent['children'][] =& $list[$key];
                    }
                }
            }
        }

        return $tree;
    }
}

if (!function_exists('convert_underline')) {
    /**
     * 下划线转驼峰
     *
     * @param $str
     *
     * @return null|string|string[]
     */
    function convert_underline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }
}

if (!function_exists('hump_to_line')) {
    /**
     * 驼峰转下划线
     *
     * @param $str
     *
     * @return null|string|string[]
     */
    function hump_to_line($str)
    {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        return $str;
    }

}

if (!function_exists('fun_alternative_name')) {
    /**
     * 三个字符或三个字符以上掐头取尾，中间用*代替
     * 俩个字符保留都不去除尾部用*代替
     *
     * @param string $str
     *
     * @return string
     */
    function fun_alternative_name($str)
    {
        if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
            //按照中文字符计算长度
            $len = mb_strlen($str, 'UTF-8');
            //echo '中文';
            if ($len > 2) {
                //三个字符或三个字符以上掐头取尾，中间用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*' . mb_substr($str, -1, 1, 'UTF-8');
            } elseif ($len == 2) {
                //俩个字符保留都不去除尾部用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
            }
        } else {
            //按照英文字串计算长度
            $len = strlen($str);
            if ($len > 2) {
                //三个字符或三个字符以上掐头取尾，中间用*代替
                $str = substr($str, 0, 1) . '*' . substr($str, -1);
            } elseif ($len == 2) {
                //俩个字符保留都不去除尾部用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
            }
        }
        return $str;
    }

}

if (!function_exists('fun_show_first_name')) {
    /**
     * 取首字符其余用*
     *
     * @param string $str
     *
     * @return string
     */
    function fun_show_first_name($str)
    {
        //去除两边空格
        $str = trim($str);
        //判断是否是中文

        return mb_substr($str, 0, 1, 'UTF-8') . '*';
    }
}

if (!function_exists('filter_by_value')) {
    /**
     * 根据二维数组中某个值获取对应的第一个一维数组数据
     *
     * @param $array
     * @param $index
     * @param $value
     * @param $showAll
     *
     * @return array
     */
    function filter_by_value($array, $index, $value, $showAll = false)
    {
        $new_array = [];
        if (is_array($array) && count($array) > 0) {
            foreach (array_keys($array) as $key) {
                $temp[$key] = $array[$key][$index];
                if ($temp[$key] == $value) {
                    $new_array = $array[$key];
                    if (!$showAll) {
                        return $new_array;
                    }
                }
            }
        }
        return $new_array;
    }
}

if (!function_exists('sort_by_key')) {
    /**
     * 二维数组按照键值升序排序
     *
     * @param array  $arr  待排序数组
     * @param string $key  键值
     * @param string $type 排序方式 默认asc升序 desc 降序
     *
     * @return mixed
     */

    function sortByKey($arr, $key, $type = 'asc')
    {
        $sort_type = SORT_ASC;
        if ($type === '' || $type === null || $type == 'asc' || $type == 'ASC') {
            $sort_type = SORT_ASC;
        } elseif ($type == 'desc' || $type == 'DESC') {
            $sort_type = SORT_DESC;
        }

        $array_column = array_column($arr, $key);
        array_multisort($array_column, $sort_type, $arr);
        return $arr;
    }
}


if (!function_exists('sort_by_key_desc')) {
    /**
     * 二维数组按照键值降序排序
     *
     * @param array  $arr 待排序数组
     * @param string $key 键值
     *
     * @return mixed
     */

    function sort_by_key_desc($arr, $key)
    {
        return sortByKey($arr, $key, 'desc');
    }
}

if (!function_exists('formatNumberWithWan')) {
    /**
     * 格式化数字 过万显示单位w 保留两位小数
     *
     * @param $v
     * @param $wan_decimals
     * @param $decimals
     * @param $unit
     *
     * @return string
     */
    function formatNumberWithWan($v, $wan_decimals = 2, $decimals = 2, $unit = 'w')
    {
        return $v > 10000 || $v < -10000 ? (sprintf('%.' . $wan_decimals . 'f',
                $v * 1 / 10000) . $unit) : sprintf('%.' . $decimals . 'f', $v * 1);
    }
}

if (!function_exists('calculateGrowthRate')) {
    /**
     * @param float|int $new
     * @param float|int $old
     *
     * @return float|int|null
     */
    function calculateGrowthRate($new, $old)
    {
        return $old == 0 ? null : (($new - $old) / $old * 100);
    }
}

if (!function_exists('formatGrowthRate')) {
    /**
     * 格式化
     *
     * @param $rate
     *
     * @return string
     */
    function formatGrowthRate($rate)
    {
        if ($rate === null) {
            return '-';
        }
        return sprintf('%.2f', $rate * 1) . ($rate > 0 ? '% ↑' : ($rate < 0 ? '% ↓' : '%'));
    }
}

if (!function_exists('getDivideInteger')) {
    /**
     * 均分正整数为多份
     *
     * @param int $number 要均分的正整数 或 0
     * @param int $total  均分的份数
     *
     * @return array|false|string[]
     */
    function getDivideInteger(int $number, int $total)
    {
        if ($number < 0 || $total <= 0) {
            return false;
        }

        // 平均整数
        $per = intval($number / $total);
        // 余数
        $rest = $number % $total;

        // 余数均分

        $number_str = str_repeat(($per + 1) . ',', $rest) . str_repeat($per . ',', $total - $rest - 1) . $per;
        return explode(',', $number_str);
    }

}

if(!function_exists('arrayLevel')) {
    /**
     * 获取数组维度
     *
     * @param array $arr
     *
     * @return mixed
     */
    function arrayLevel(array $arr){
        $al = [0];
        function aL($arr,&$al,$level=0){
            if(is_array($arr)){
                $level++;
                $al[] = $level;
                foreach($arr as $v){
                    aL($v,$al,$level);
                }
            }
        }
        aL($arr,$al);
        return max($al);
    }

}

if (!function_exists('multiCollectIntersect')) {
    /**
     * 二维数组集合交集（返回第一个集合中的交集数据）
     *
     * @param \Illuminate\Support\Collection $collect1
     * @param \Illuminate\Support\Collection $collect2
     *
     * @return \Illuminate\Support\Collection
     */
    function multiCollectIntersect(\Illuminate\Support\Collection $collect1, \Illuminate\Support\Collection $collect2)
    {

        if ($collect1->count() == 0 || $collect2->count() == 0) {
            return collect([]);
        }

        return $collect1->filter(function ($v) use ($collect2) {
            foreach ($collect2 as $key => $val) {
                if (count(array_intersect_assoc($val, $v)) > 0) {
                    return true;
                }
            }
            return false;
        });

    }

}

if (!function_exists('multiCollectDiff')) {
    /**
     * 二维数组集合差集（返回第一个集合中的差集数据）
     *
     * @param \Illuminate\Support\Collection $collect1
     * @param \Illuminate\Support\Collection $collect2
     *
     * @return \Illuminate\Support\Collection
     */
    function multiCollectDiff(\Illuminate\Support\Collection $collect1, \Illuminate\Support\Collection $collect2)
    {

        if ($collect1->count() == 0 || $collect2->count() == 0) {
            return collect([]);
        }

        return $collect1->filter(function ($v) use ($collect2) {
            foreach ($collect2 as $key => $val) {
                if (count(array_intersect_assoc($val, $v)) > 0) {
                    return false;
                }
            }
            return true;
        });

    }

}

if (!function_exists('multiArrayIntersect')) {
    /**
     * 二维数组差集（返回第一个数组中的差集数据）
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    function multiArrayIntersect(array $array1, array $array2)
    {
        if (count($array1) == 0 || count($array1) == 0) {
            return [];
        }

        return array_filter($array1, function ($v) use ($array2) {
            foreach ($array2 as $key => $val) {
                if (count(array_intersect_assoc($val, $v)) > 0) {
                    return true;
                }
            }

            return false;
        });
    }
}

if (!function_exists('multiArrayDiff')) {
    /**
     * 二维数组差集（返回第一个数组中的差集数据）
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    function multiArrayDiff(array $array1, array $array2)
    {
        if (count($array1) == 0 || count($array1) == 0) {
            return [];
        }

        $arr1Level = arrayLevel($array1);
        $arr2Level = arrayLevel($array2);
        if($arr1Level != $arr2Level) {
            return [];
        }

        if($arr1Level == 1) {
            if(is_assoc($array1) && is_assoc($array2)) {
                return array_diff($array1, $array2);
            }
            return array_diff_assoc($array1, $array2);
        }

        if($arr1Level == 2) {
            return array_filter($array1, function ($v) use ($array2) {
                foreach ($array2 as $key => $val) {
                    if (count(array_intersect_assoc($val, $v)) > 0) {
                        return false;
                    }
                }

                return true;
            });
        }

    }
}
