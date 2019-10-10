<?php
/**
 * 验证类
 *
 * @author youling073
 * @date 2019/10/10 16:59
 *
 */
namespace Youling073\Easytool\Validate;

use Youling073\Easytool\Validate\Idcard;

class Validate {

    /**
     * 验证中文名
     *
     * @param string $name
     * @return bool
     */
    public function isChineseName(string $name) : bool
    {
        $res = false;
        $preg_name='/^[\x{4e00}-\x{9fa5}]{2,5}$/isu';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证身份证号
     *
     * @param string $idcard
     * @return bool
     * @throws \Exception
     */
    public function isIdcard(string $idcard) : bool
    {
        $res = false;

        $idcard_obj = new Idcard();
        $res = $idcard_obj->isValidate($idcard);

        return $res;
    }

    /**
     * 验证QQ
     *
     * @param string $name
     * @return bool
     */
    public function isQQ(string $name) : bool
    {
        $res = false;
        $preg_name='/^\d{5,12}$/isu';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证银行卡
     *
     * @param string $name
     * @return bool
     */
    public function isBankcard(string $name) : bool
    {
        $res = false;
        $preg_name='/^(\d{15}|\d{16}|\d{19})$/isu';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证中文英文名
     *
     * @param string $name
     * @return bool
     */
    public function isChineseEnName(string $name) : bool
    {
        $res = false;
        $preg_name='/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证手机号
     *
     * @param string $name
     * @return bool
     */
    public function isPhoneNum(string $name) : bool
    {
        $res = false;
        $preg_name='/^1\d{10}$/ims';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证邮箱
     *
     * @param string $name
     * @return bool
     */
    public function isEmail(string $name) : bool
    {
        $res = false;
        $preg_name='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 验证特殊符号(如需要验证其他字符，自行转义 "\X" 添加)
     *
     * @param string $name
     * @return bool
     */
    public function isSpacialChar(string $name) : bool
    {
        $res = false;
        $preg_name='/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\/|\;|\\\' | \`|\-|\=|\\\|\|/isu';
        if(preg_match($preg_name,$name)){
            $res = true;
        }
        return $res;
    }

    /**
     * 检查密码在某个区间之内，切中英文混合
     *
     * @param string $password
     * @param int $min
     * @param int $max
     * @return bool
     */
    public function checkPassword(string $password, int $min = 8, int $max = 20) : bool
    {
        $password = trim($password);
        if (empty($password)) {
            return false;
        }

        // 匹配8~20位由数字和26个英文字母混合而成的密码：
        $password_reg = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{'.$min.','.$max.'}$/';
        if (!preg_match($password_reg, $password)) {
            return false;
        }

        return true;
    }
}
