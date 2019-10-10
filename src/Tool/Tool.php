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
     */
    public function defineTxtView($arr, $flag_key = 'id')
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
    public function filter_ids(array $id_arr, $is_unique = true)
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

}