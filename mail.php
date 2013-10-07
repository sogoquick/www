<?php
require_once 'phpmailer/class.phpmailer.php';
class SendMail {

    private static $instance;

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new SendMail();
        }
        return self::$instance;
    }

    public function send($file) {
        if(empty($file) || !file_exists($file)) {
            echo "File can't be find";
            return '';
        }
         //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
         $mail = new PHPMailer(true); 

         try {
             $mail->CharSet = "UTF-8";
             $mail->IsSMTP();
             $mail->SMTPDebug = 1;
         
             $mail->SMTPAuth = true;
             //$mail->SMTPSecure = "ssl";
             $mail->Host = "smtp.anjuke.com";
             $mail->Port = 25;
             $mail->Username = "hanjunfeng@anjuke.com";
             $mail->Password = "xxxx";
             $mail->AddReplyTo('hanjunfeng@anjuke.com', 'First Last');
             $mail->AddAddress('hanjunfeng_39@iduokan.com', 'Feng');
             $mail->SetFrom('hanjunfeng@anjuke.com', 'Me');
             $mail->Subject = 'FT chinese';
             $mail->AltBody = 'Auto Sendmail with attachment'; 
             $mail->MsgHTML('From Phpmailer');
             $mail->AddAttachment($file);      // attachment
             $mail->Send();
             echo "Message Sent OK<p></p>\n";
         } catch (phpmailerException $e) {
             echo $e->errorMessage(); //Pretty error messages from PHPMailer
         } catch (Exception $e) {
             echo $e->getMessage(); //Boring error messages from anything else!
         }
    }

    public function send_mail($address,$title = "Nothing",$content = "Nothing") {
        if(empty($address)) {
            echo "Please give your mail address";
            return '';
        }
         //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
         $mail = new PHPMailer(true); 

         try {
            $mail->CharSet = "UTF-8";
            $mail->IsSMTP();
            $mail->SMTPDebug = 1;
            
            $mail->SMTPSecure = "ssl";
            $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
            $mail->Host = "smtp.126.com";
            $mail->Port = 994;                  // SMTP服务器的端口号
            $mail->Username   = "sogoquick";  // SMTP服务器用户名
            $mail->Password   = "guilin123";            // SMTP服务器密码
            $mail->SetFrom('sogoquick@126.com', 'coldarmy');
            $mail->AddReplyTo("sogoquick@126.com","coldarmy");
            $mail->Subject    = $title;
            $mail->AltBody    = $content; // optional, comment out and test
            $mail->MsgHTML($content);
            $mail->AddAddress($address, "Feng");
            $mail->Send();
             echo "Message Sent OK<p></p>\n";
         } catch (phpmailerException $e) {
             echo $e->errorMessage(); //Pretty error messages from PHPMailer
         } catch (Exception $e) {
             echo $e->getMessage(); //Boring error messages from anything else!
         }
    }

    private function __clone(){
    
    }
    private function __construct() {
    
    }
}
?>
