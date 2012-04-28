<?php
/**
 * 发送邮件类，基于mail函数
 * 基于php 的mail函数发送邮件
 * 需要配置服务器的smtp
 * 如时linux 请安装sendmail 在php.ini里面开启
 * sendmail_path = /usr/sbin/sendmail -t -i
 * **/
class Mail {

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new Mail();
        }
        return self::$instance;
    }

    /**
     * 发送邮件，基于mail 方法
     *@param mail address $to
     *@param string $subject
     *@param string $body
     *@param mail address $frommail
     *@param string $fromtitle
     *@param string $encoding
     *@return true|fasle
     * */
    public function sendmail($to,$body,$subject,$frommail,$fromtitle,$encoding = 'utf-8') {
        $result = 0;
        if(empty($to)) {
            return $result;
        }
        $header = $this->build_header($fromtitle, $frommail,$encoding);
        $subject = $this->build_mail_base64($this->iconv($subject,$encoding),$encoding);
        $body = $this->iconv($body,$encoding);
        $mail = mail('hanjunfeng@anjuke.com', $subject, $body,$header);
        if($mail) {
            $result = 1;
        }
        return $result;
    }

    /**
     * 构造mail 邮件header信息
     * @param string $title 邮件来源标题
     * @param string $from 邮件来源地址
     * @param string $encoding 编码格式
     * @return string
     * */
    private function build_header($title,$from,$encoding = 'utf-8') {
        $fromsource = $this->build_mail_base64($this->iconv($title,$encoding),$encoding);
        $fromaddress = 'MIME-Version: 1.0' . "\r\n";
        $fromaddress .= 'Content-type: text/plain; charset='.$encoding. "\r\n";
        $fromaddress .= 'From:'.$fromsource.' <'.$from.">\r\n";
        return $fromaddress;
    }

    /**
     * 构建基于邮件内容的base64编码，默认utf-8
     * @param string $data
     * @param string $encoding 编码格式
     * @return string
     * */
    private function build_mail_base64($data,$encoding = 'utf-8') {
        if(empty($encoding)) {
            return '';
        }
        return '=?'.$encoding.'?B?'.base64_encode($data).'?=';
    }

    /**
     * 转换为utf-8格式编码
     * @param string $data'
     * @return string
     * */
    private function iconv($data,$encoding = 'utf-8') {
        if(empty($encoding) || empty($data)) {
            return $data;
        }
        $encoding = strtoupper($encoding);
        //获取字符串编码格式
        $current_encode = mb_detect_encoding($data);
        /* 如果非utf-8 格式，转换为utf-8 格式 */
        if($current_encode != $encoding) {
            $data = iconv($current_encode, $encoding, $data);
        }
        return $data;
    }

    private static $instance;

    /**
     * 单列
     * */
    private function __construct() {

    }

    /**
     * 禁止clone
     * */
    private function __clone() {

    }
}
