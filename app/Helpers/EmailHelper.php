<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮箱帮助类
 * App\Helpers
 * @author: qiaohao
 * @Time: 2023/3/4 15:09
 */
class EmailHelper
{
    private $host="smtp.163.com";//SMTP服务器
    private $Username="Aricoin123@163.com";//即邮箱的用户名
    private $Password="JEREZBAWJORYXVSB";//SMTP 密码
    private $fromAddress="Aricoin123@163.com";//发件人
    private $fromName="DET";//发件人
    private $ReplyToAddress="fhzx168168@163.com";//回复邮箱
    private $ReplyName="DET";//回复人
    /**
     * 发送
     * User: qiaohao
     * Date: 2023/3/4 15:10
     */
    private function send($email,$name,$subject,$body,$altBody)
    {
        $mail = new PHPMailer(true);
        //服务器配置
        $mail->CharSet = "UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = $this->host;                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = $this->Username;      // SMTP 用户名  即邮箱的用户名
        $mail->Password = $this->Password;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom($this->fromAddress, $this->fromName);  //发件人
        $mail->addAddress($email, $name);  // 收件人
        //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        $mail->addReplyTo($this->ReplyToAddress, $this->ReplyName); //回复的时候回复给哪个邮箱 建议和发件人一致
        //$mail->addCC('cc@example.com');//抄送
        //$mail->addBCC('bcc@example.com');//密送

        //发送附件
        // $mail->addAttachment('../xy.zip');// 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');// 发送附件并且重命名

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $subject;
        $mail->Body = $body;//"您的验证码为{$code}，请勿告诉他人！！！";
        $mail->AltBody = $altBody;//'您的验证码为{$code}，请勿告诉他人！！！';
        $mail->send();
    }

    /**
     * 发送短信验证码
     * @param $email
     * @param $name
     * @param $code
     * User: qiaohao
     * Date: 2023/3/4 15:27
     */
    public function sendCode($email,$name,$code){
        $subject="Registration verification code";
        $body="您的验证码为{$code}，5分钟内有效，请勿告诉他人！！！";
        $altBody="您的验证码为{$code}，5分钟内有效，请勿告诉他人！！！";
        $this->send($email,$name,$subject,$body,$altBody);
    }
}
