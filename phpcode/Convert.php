<?php
/** 字符,UTF-8 格式的十六进制相互转换类
 *@author coldarmy
 *@copyright 2011
 * */
class Wenda_Util_Convert {
    /**
     * 字符转换为utf-8 格式的十六进制
     * @param string @str
     * @return string
     * */
    public static function convert_to_encoding($str) {
        $result = '';
        if(empty($str)) {
            return $result;
        }
        $current_encode = mb_detect_encoding($str);//获取字符串编码格式
        /* 如果非utf-8 格式，转换为utf-8 格式 */
        if($current_encode != 'UTF-8') {
            $str = iconv($current_encode, 'UTF-8', $str);
        }
        $len = strlen($str);
        for($i=0; $i<$len; $i++) {
            if(ord($str[$i]) == 32) { //空格替换为'+'
                $result .= '+';
            } elseif(preg_match('/[a-zA-Z]/',$str[$i])) {//字母加z前缀
                $result .= 'z'.$str[$i];
            } else { //其它输出大写十六进制
                $result .= strtoupper(dechex(ord($str[$i])));
            }
        }
        return $result;
    }
    /**
     * utf-8 格式的十六进制转换为字符串
     * @param string @str 十六进制格式
     * @return string
     * */
    public static function convert_to_decoding($str) {
        $result = '';
        if(empty($str)) {
            return $result;
        }
        /* 把 '+' 替换为空格十六进制 */
        $str = preg_replace('/\+/','20',$str);
        $len = strlen($str);
        for($i=0; $i<$len; $i +=2) {
            $two = substr($str,$i,2);
            if(preg_match('/z([a-zA-Z])/',$two)) {
                /* 对a-z A-Z 字母去掉z 直接输出 */
                $result .= preg_replace('/z/','',$two);
            } elseif(preg_match('/[8-9A-F][0-9A-F]/',$two)) {
                /* 对中文字符进行转换 */
                $second = substr($str,$i+2,2);
                $three  = substr($str,$i+4,2);
                /* 防止篡改3 Bytes 的中文编码 */
                if(preg_match('/[8-9A-F][0-9A-F]/',$second) && preg_match('/[8-9A-F][0-9A-F]/',$three)) {
                    $result .=chr(hexdec($two)).chr(hexdec($second)).chr(hexdec($three));
                } else {
                    $result = '';
                    break;
                }
                $i = $i+4;
            } else {
                /* 对其它十六进制字符输出 */
                $result .= chr(hexdec($two));
            }
        }
        return $result;
    }
}