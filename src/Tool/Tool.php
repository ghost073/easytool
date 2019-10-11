<?php
/**
 * 工具类
 *
 * @author youling073
 * @date 2019/10/10 17:45
 *
 */

namespace Youling073\Easytool\Tool;

class Tool
{
    /**
     * 生成密码
     *
     * @param $password 密码
     * @return bool|string
     *
     */
   public function genPassword($password) {
        $password = trim($password);
        if ($password === '') {
            return false;
        }

        $options = [
            'cost' => 12,
        ];
        $hash = password_hash($password, PASSWORD_BCRYPT, $options);
        return $hash;
   }

    /**
    * 验证密码
    *
    * @param $password  密码
    * @param $hash  密码hash值
    * @return bool
    */
    public function verifyPassword($password, $hash)
    {
        if (password_verify($password, $hash)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 定义格式化，用id做key
     *
     * @param  array    $arr        ['one'=>['id'=>1,'title'=>'一'], 'two'=>['id'=>2,'title'=>'二']]
     * @param string    $flag_key   哪个key做id
     *
     * @return array
     */
    public function defineTxtView(array $arr, string $flag_key = 'id') : array
    {
        $new_arr = [];
        foreach ($arr as $key => $val) {
            $new_arr[$val[$flag_key]] = $val;
        }
        return $new_arr;
    }

    /**
     * 获得前端js使用json 数据 ， 多维数组
     *
     * @param $arr  从这个数据中取值
     * @param array $need_keys  取哪些key值
     * @param bool $is_hash  是否使用hash 格式，避免json自动排序问题
     * @return array
     */
    public function getJsJsonArr($arr, $need_keys = [], $is_hash = false)
    {
        $new_arr = [];
        foreach ($arr as $key => $val) {
            $tmp = $this->getJsJsonData($val, $need_keys);

            if ($is_hash == true) {
                $new_arr['a'.$key] = $tmp;
            } else {
                $new_arr[] = $tmp;
            }
        }
        return $new_arr;
    }

    /**
     * 获得前端js使用json 数据 ， 多维数组
     *
     * @param $arr  从这个数据中取值
     * @param array $need_keys  取哪些key值
     * @param bool $is_hash  是否使用hash 格式，避免json自动排序问题
     * @return array
     */
    public function getJsJsonData($arr, $need_keys = [])
    {
        $tmp = [];
        if (empty($need_keys)) {
            // 取得所有数据
            $tmp = $arr;
        } else {
            foreach ($need_keys as $nv) {
                $tmp[$nv] = $arr[$nv] ?? '';
            }
        }
        return $tmp;
    }

    /**
     * 格式化金钱函数 元
     *
     * @param    int     $money        金钱
     *
     * @return   string              格式化的金钱
     */
    public function formatMoneyYuan($money)
    {
        $money = intval($money);
        $money_yuan = $money/100;
        return $money_yuan;
    }

    /**
     * 格式化数据化手机号码, 隐藏中间号码
     *
     * @param $mobile
     * @return string
     */
    public function formatMobile($mobile)
    {
        return substr($mobile,0,5)."****".substr($mobile,9,2);
    }

    /**
     * 单位自动转换函数
     * @param $size
     * @return string
     */
    public function getRealSize($size)
    {
        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte

        if($size < $kb)
        {
            return $size . 'B';
        }
        else if($size < $mb)
        {
            return round($size/$kb, 2) . 'KB';
        }
        else if($size < $gb)
        {
            return round($size/$mb, 2) . 'MB';
        }
        else if($size < $tb)
        {
            return round($size/$gb, 2) . 'GB';
        }
        else
        {
            return round($size/$tb, 2) . 'TB';
        }
    }

    /**
     * url参数转换为数组
     * @param $query
     * @return array
     */
    public function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     * 过滤ID只返回ID>0的非重复值
     *
     *
     */
    public function filterIds(array $id_arr, $is_unique = true)
    {
        if (empty($id_arr)) {
            return [];
        }

        //过滤
        foreach ($id_arr as $key => $value) {
            $value = intval($value);
            if ($value < 1)
            {
                unset($id_arr[$key]);
                continue;
            }

            $id_arr[$key] = $value;
        }

        if ($is_unique == true) {
            $id_arr = array_values(array_unique($id_arr));
        } else {
            $id_arr = array_values($id_arr);
        }
        return $id_arr;
    }


    /**
     * 获取图片的URL地址
     * 如果是携带http 不做处理否则拼接上域名
     *
     * @param string $url
     * @param string $domain
     * @return string
     */
    public function getImgUrl(string $url, string $domain = '') : string
    {
        $url = trim($url);
        if (!preg_match('/(http:|https:)/i', $url)){
            $url = $domain.$url;
        }
        return $url;
    }

    /**
     * ali oss 获得上传视频截取的第一帧
     *
     *
     * @param string $url
     * @param int $width
     * @param int $height
     * @param string $domain
     * @return string
     *
     * https://help.aliyun.com/document_detail/64555.html?spm=a2c4g.11186623.6.1317.2607c1f6MDUDOX
     */
    public function aliossVideoImgUrl(string $url, int $width=800, int $height=600, string $domain = '') : string
    {
        $url = trim($url);
        if (!preg_match('/(http:|https:)/i', $url)){
            $url = $domain.$url;
        }
        $url .= '?x-oss-process=video/snapshot,t_8000,f_jpg,w_%s,h_%s,m_fast';
        $url = sprintf($url, $width, $height);

        return $url;
    }

    /**
     * ali oss 获得上传图片缩略图
     *
     * @param string $url
     * @param int $width
     * @param int $height
     * @param string $domain
     * @return string
     *
     * https://help.aliyun.com/document_detail/44688.html?spm=a2c4g.11174283.6.1237.19d07da200ehfg
     */
    public function aliossResizeImgUrl(string $url, int $width=800, int $height=600, string $domain = '') : string
    {
        $url = trim($url);
        if (!preg_match('/(http:|https:)/i', $url)) {
            $url = $domain.$url;
        }
        $url .= '?x-oss-process=image/resize,m_fill,h_%s,w_%s';
        $url = sprintf($url, $height, $width);

        return $url;
    }

    /**
     * 生成指定长度随机字符串
     *
     * @param int $num  字符串长度
     * @return string
     */
    public function randomStr(int $num = 6) : string
    {
        // 参数过滤
        $num = intval($num);
        if ($num < 1) {
            $num = 6;
        }
        // 生成密码使用的初始值
        $init_str = '0123456789abcdefghijklmnopqrstuvwxyz';
        // 返回的值
        $new_str = '';

        for ($i = 1; $i <= $num; $i ++) {
            // 字符串顺序随机
            $str = str_shuffle($init_str);
            // 截取指定长度
            $str = substr($str, $i, 1);

            $new_str .= $str;
        }

        return $new_str;
    }

    /**
     * 获得两个值的数值比
     *
     * @param int $num1
     * @param int $num2
     * @return string
     */
    public function getNumPercent(int $num1, int $num2) : string
    {
        $num2 = max(1, $num2);
        $num = ($num1 / $num2) * 100;

        $number = sprintf("%1\$.2f%%",$num);
        return $number;
    }
}