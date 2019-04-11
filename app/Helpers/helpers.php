<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 14:15
 */

if(! function_exists('arrayGroup')){
    /**
     * 将二维数组根据某一个key进行分组重组
     * @param $array
     * @param $group_key
     * @return array
     */
    function arrayGroup($array,$group_key)
    {

        if(!$array){
            return [];
        }

        $isStdClass=false;
        if(!is_array($array[0])){
            $isStdClass =true;
        }

        $cur_arr=[];   //current row
        $result = [];
        foreach($array as $item){
            if($isStdClass){
                $cur_arr = (array)$item;
            } else {
                $cur_arr = $item;
            }

            if(!array_key_exists($group_key,$cur_arr)){
                return [];
            }

            $result[$cur_arr[$group_key]][] =$cur_arr;

        }

        unset($cur_arr);

        return $result;

    }
}

if(! function_exists('generateSMSCode')){
    /**
     * 生成指定长度的数字验证码
     * @param $len
     * @return bool|string
     */
    function generateSMSCode($len)
    {

        $len = intval($len);
        if($len === 0){
            return false;
        }

        $code = '';
        for ($i=0; $i < $len; $i++) {
            $code = $code . rand(0, 9);
        }

        return $code == '' ? false : $code;
    }
}

if(! function_exists('deepInArray')){
    /**
     * 判断一个多维数组中是否存在某一个值
     * @param $value
     * @param $array
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

            if(in_array($value, $item)) {
                return true;
            } else if(deepInArray($value, $item)) {
                return true;
            }
        }
        return false;
    }
}

if(! function_exists('isJson')){
    /**
     * 判断一个字符串是否是有效的json字符串
     * @param $string
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if(! function_exists('isMobile')){
    /**
     * 验证手机号码是否正确
     * @param String $mobile 手机号码
     * @return boolean
     */
    function isMobile($mobile)
    {
        //手机号码验证规则
        $regx = "/^((1[3,4,5,7,8][0-9])|(14[5,6,7,8,9])|(16[6])|(19[9]))\d{8}$/";

        if(preg_match($regx,$mobile)){
            return true;
        }else{
            return false;
        }
    }
}

if(! function_exists('isAllChinese')){
    /**
     * 判断姓名是否全是中文
     * @param $str
     * @return bool
     */
    function isAllChinese($str){
        //新疆等少数民族可能有·
        if(strpos($str,'·')){
            //将·去掉，看看剩下的是不是都是中文
            $str=str_replace("·",'',$str);
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }else{
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }
    }
}

if(! function_exists('isIDCard')){
    /**
     * 验证身份证号码是否正确
     * @param String $id 身份证号码
     * @return boolean
     */
    function isIDCard( $id = '' )
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match($regx, $id))
        {
            return false;
        }
        if(15==strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth))
            {
                return false;
            } else {
                return true;
            }
        }
        else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return false;
            }
            else
            {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ( $i = 0; $i < 17; $i++ )
                {
                    $b = (int) $id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id,17, 1))
                {
                    return false;
                } //phpfensi.com
                else
                {
                    return true;
                }
            }
        }

    }
}

if(! function_exists('numberToChinese')){
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
                $n = substr($num, strlen($num)-1, 1);
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
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零圆' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }
}

if(! function_exists('loadImg')){
    /**
     * 保存网络图片到服务器
     * 小程序传的头像是网络地址需要周转一下
     * @param $image_url
     * @param $local_url
     * @return bool|int
     */
    function loadImg($image_url,$local_url)
    {
        $img_file = file_get_contents($image_url);
        $img_content = base64_encode($img_file);
        $result = file_put_contents($local_url, base64_decode($img_content));

        return $result;
    }
}

if(! function_exists('getClientIp')){
    /**
     * 获取客户端 ip
     * @return array|false|null|string
     */
    function getClientIp()
    {
        static $realip = NULL;
        if ($realip !== NULL) {
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

if(! function_exists('issetAndNotEmpty')){
    /**
     * 判断数组的键是否存在，并且佱不为空
     * @param $arr
     * @param $column
     * @return null
     */
    function issetAndNotEmpty($arr, $column)
    {
        return (isset($arr[$column]) && $arr[$column]) ? $arr[$column] : '';
    }
}

if(! function_exists('trimAllBlankSpace')){
    /**
     * 过滤用户输入数据中的空格 全角空格 tab
     * @param $str
     * @return mixed
     *
     */
    function trimAllBlankSpace($str)
    {
        $search = array(" ", "　", "\t");
        $replace = array("", "", "");
        return str_replace($search, $replace, $str);
    }
}

if(! function_exists('getHourAndMin')){
    /**
     * 将时间戳转换成 xx 时\xx 分
     * @param $time
     * @return array
     */
    function getHourAndMin($time)
    {
        $sec = round($time / 60);
        if ($sec >= 60) {
            $hour = floor($sec / 60);
            $min = $sec % 60;

        } else {
            $hour = 0;
            $min = $sec;
        }
        return ['hour' => $hour, 'min' => $min];
    }
}

if(! function_exists('getTowPositionDistance')){
    /**
     * 根据经纬度获取两点间的直线距离，返回 KM
     * @param $lon1
     * @param $lat1
     * @param $lon2
     * @param $lat2
     * @return float
     */
    function getTowPositionDistance($lon1, $lat1, $lon2, $lat2)
    {
        $radius = 6378.137;
        $rad = floatval(M_PI / 180.0);

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

if(! function_exists('geOrderSn')){
    /**
     * 生成唯一订单号
     * @param string $pre 前缀
     * @param string $table_name 存放订单数据库名称
     * @param string $column 订单号字段名
     * @return string 订单号
     */
    function geOrderSn($pre = '', $table_name = '', $column = 'order_sn')
    {
        mt_srand((double)microtime() * 1000000);

        $str = $pre . date('Ymd') . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);;
        if ($table_name && $column) {
            $sn = \Illuminate\Support\Facades\DB::table($table_name)->where($column, $str)->count();
            if ($sn > 0) {
                geOrderSn($pre, $table_name, $column);
            }
        }
        return $str;
    }
}

if(! function_exists('httpPostNoRest')){
    /**
     * 发起一个http 请求
     * @param $url
     * @param $data
     * @return bool|string
     */
    function httpPostNoRest($url, $data)
    {
        $post_data = http_build_query(
            $data
        );

        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}

if(! function_exists('httpPostRequest')){
    /**
     * http post请求
     * @param $url
     * @param array $params
     * @return mixed
     */
    function httpPostRequest($url, array $params)
    {
        $params = json_encode($params, JSON_FORCE_OBJECT);
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

if(! function_exists('httpGetRequest')){
    /**
     * curl一个http get请求
     * @param $url
     * @param $params
     * @return mixed
     */
    function httpGetRequest($url, string $params)
    {
        $url = $url . '?' . http_build_query($params);
        $ch = curl_init();
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

if (! function_exists('addslashesDeep'))
{
    /**
     * 递归方式的对变量中的特殊字符进行转义
     * @param $value
     * @return array|string
     */
    function addslashesDeep($value)
    {
        if (empty($value))
        {
            return $value;
        }
        else
        {
            return is_array($value) ? array_map('addslashesDeep', $value) : addslashes($value);
        }
    }
}

if(! function_exists('userTextEncode')){
    /**
     * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
     * @param $str
     * @return mixed|string
     */
    function userTextEncode($str)
    {
        if(!is_string($str))return $str;
        if(!$str || $str=='undefined')return '';

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
            return addslashes($str[0]);
        },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }
}

if(! function_exists('userTextDecode')){
    /**
     * 解码userTextEncode转义的内容 与 userTextEncode配对使用
     * @param $str
     * @return mixed
     */
    function userTextDecode($str){
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i',function(){
            return '\\';
        },$text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }
}

if(!function_exists('getInitials')){
    /**
     * 获取首字母
     * @param  string $str 汉字字符串
     * @return string 首字母
     */
    function getInitials($str){
        if (empty($str)) return '#';
        $fChar = ord($str{0});
        if ($fChar >= ord('A') && $fChar <= ord('z'))
            return strtoupper($str{0});

        $s1  = iconv('UTF-8', 'gb2312', $str);
        $s2  = iconv('gb2312', 'UTF-8', $s1);
        $s   = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284)
            return 'A';


        if ($asc >= -20283 && $asc <= -19776)
            return 'B';


        if ($asc >= -19775 && $asc <= -19219)
            return 'C';


        if ($asc >= -19218 && $asc <= -18711)
            return 'D';


        if ($asc >= -18710 && $asc <= -18527)
            return 'E';


        if ($asc >= -18526 && $asc <= -18240)
            return 'F';


        if ($asc >= -18239 && $asc <= -17923)
            return 'G';


        if ($asc >= -17922 && $asc <= -17418)
            return 'H';


        if ($asc >= -17417 && $asc <= -16475)
            return 'J';


        if ($asc >= -16474 && $asc <= -16213)
            return 'K';


        if ($asc >= -16212 && $asc <= -15641)
            return 'L';


        if ($asc >= -15640 && $asc <= -15166)
            return 'M';


        if ($asc >= -15165 && $asc <= -14923)
            return 'N';


        if ($asc >= -14922 && $asc <= -14915)
            return 'O';


        if ($asc >= -14914 && $asc <= -14631)
            return 'P';


        if ($asc >= -14630 && $asc <= -14150)
            return 'Q';


        if ($asc >= -14149 && $asc <= -14091)
            return 'R';


        if ($asc >= -14090 && $asc <= -13319)
            return 'S';


        if ($asc >= -13318 && $asc <= -12839)
            return 'T';


        if ($asc >= -12838 && $asc <= -12557)
            return 'W';


        if ($asc >= -12556 && $asc <= -11848)
            return 'X';


        if ($asc >= -11847 && $asc <= -11056)
            return 'Y';


        if ($asc >= -11055 && $asc <= -10247)
            return 'Z';


        return '#';
    }
}

if(!function_exists('getArrayRepeat')){
    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值
     * @param array $arr  目标数组
     * @param array $keys  要进行判断的键值组合的数组
     * @return array 重复的值
     */
    function getArrayRepeat($arr = [],$keys = []) {
        $unique_arr = array();
        $repeat_arr = array();
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if( !in_array($str, $unique_arr) ){
                $unique_arr[] = $str;
            } else {
                $repeat_arr[] = $v;
            }
        }
        return $repeat_arr;
    }
}

if(!function_exists('hasArrayRepeat')){
    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值
     * @param array $arr  目标数组
     * @param array $keys  要进行判断的键值组合的数组
     * @return bool 是有重复 true有重复 false无重复
     */
    function hasArrayRepeat($arr = [],$keys = []) {
        $unique_arr = array();
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if( !in_array($str, $unique_arr) ){
                $unique_arr[] = $str;
            } else {
                return true;
            }
        }
        return false;
    }
}

if(!function_exists('is_assoc')){
    /**
     * 作用：检测数组是否为索引数组。
     * @param array $arr 传入的数组
     * @return bool
     */
    function is_assoc(array $arr)
    {
//        return array_keys($arr) !== range(0, count($arr) - 1);
        return (bool)count(array_filter(array_keys($arr), 'is_string'));
    }
}
