<?php
class Mailer {

    public static function SendMail($type = '', $msg, $msgsubject, $mailto, $mailtoname = '', $mailBCC = '', $attachment = '', $attachmentname = '') {

        $msg_html = '';
        $msg_html .= self::getMailHeader(strtoupper($msgsubject));
        if ($type == 'uc') {
            $msg_html .= self::getUserHead(ucwords($mailtoname));
            $msg_html .= $msg;
            $msg_html .= self::getUserFoot();
        } else {
            $msg_html .= $msg;
        }

        $msg_html .= self::getMailFooter();

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = false;
        $mail->Host = MAILER_SMTP;
        $mail->Port = MAILER_PORT;
        $mail->SMTPSecure = "ssl";
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        $mail->SMTPAuth = true;
        $mail->Username = MAILER_USER;
        $mail->Password = MAILER_PASS;
        $mail->setFrom(MAILER_FROM_EMAIL, MAILER_FROM_NAME);
        if(defined("MAILER_REPLYTO_EMAIL")){
            $mail->AddReplyTo(MAILER_REPLYTO_EMAIL, MAILER_REPLYTO_NAME);
        }
        $mail->isHTML(true);
        $mail->addAddress($mailto, $mailtoname);

        if (strlen($attachment) > 0) {
            $mail->addAttachment($attachment, $attachmentname);
        }

        if ($mailBCC <> '') {
            $arrBcc = explode(',', $mailBCC);
            foreach ($arrBcc as $bcc) {
                $mail->addBCC($bcc);
            }
        }
        $mail->Subject = $msgsubject;
        $mail->msgHTML($msg_html);
        return $mail->send();
    }

    private static function getMailHeader($subject) {
        return $head_cnt = "<!doctype html><html> <head> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/> <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"> <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\"> <title>Al-Shuaib</title> <style> .email-body-table {margin:0px auto;} p{margin:0px;}body{font-size:12px !important;}.bg-white{background-color:#ffffff;}a{text-decoration:none;}.center-wrapper{border-left:5px solid #1AD4A2; border-right:5px solid #1AD4A2;}.email-body{display:block; margin:0% 7% 0%;}.email-body p{margin:0px;}.social-icons{width:241px; height:50px;}@media only screen and (max-width: 767px){.bg-white{width:100% !important;}.email-body h1{font-size:16px; margin:0px;}.social-icons{width:266px; height:50px; margin:0 auto; display:block;}.social-icons a{margin-right:5px !important; width:32px; height:32px;}}@media only screen and (max-width: 479px) {.button-block{width: 95% !important; display: block !important; text-align: center; clear: both;}}</style> </head> <body style=\"font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; width:100%; height:100%; font-size:12px !important; background-color:#fff; margin:0; padding:0; color:#333333;\"> <table width=\"100%\" bgcolor=\"\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"> <tbody> <tr> <td style=\"\"> <table class=\"bg-white\" style=\"background-color:#ffffff;\" cellpadding=\"0\" bgcolor=\"#fff\" cellspacing=\"0\" width=\"581\" border=\"0\" align=\"center\"> <tbody> <tr> <td align=\"center\" style=\"background:#1AD4A2;\"> <table style=\"background:#1AD4A2;\" class='table-space'><tbody><tr><td height=\"25\"  ></td></tr></tbody></table><div class=\"email-header\"> <a href=\"".ABS_URL."\" style=\"display:block; background:#1AD4A2; width:100%; margin:0 auto; text-align:center;\"> <img src=\"".ABS_URL."img/logo/alshuaib-header-logo.png\" alt=\"\" style=\"display:block; border:0; margin:0px auto;\"> </a> </div><table  style=\"background:#1AD4A2;\" class='table-space'><tbody><tr><td height=\"25\"></td></tr></tbody></table><table class=\"center-wrapper\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" bgcolor=\"#f7f7f7\"> <tbody> <tr valign=\"top\"> <td align=\"left\" colspan=\"4\" style=\"padding-top:23px; padding-bottom:23px;\"> <div style=\"padding:7px 0px;\" class=\"email-body\"> <h1 style=\"text-align:center; font-size:18px;\">$subject</h1><br>";
    }

    private static function getMailFooter() {
        return $food_cnt = "</div></td></tr></tbody> </table> <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#1AD4A2\" width=\"581\" align=\"center\"> <tbody> <tr valign=\"top\"> <td align=\"center\" style=\"padding-top:23px;\" > <div class=\"email-footer\"> <p class=\"social-icons\" style=\"text-align:center; margin:0 auto;\"> <a href=\"https://www.facebook.com/Al-Shuaib-International-Financial-Brokerage-Co-الشعيب-للوساطه-الماليه-1626091544352133/\" target=\"_blank\" style=\"float:left; width:48px; height:32px; margin:6px 0px 10px 0; background:#1AD4A2;\" > <img src=\"".ABS_URL."img/facebook.png\" alt=\"facebook\" style=\"display:block; margin:0;\"> </a> <a href=\"https://twitter.com/alshuaibfx\" target=\"_blank\" style=\"float:left; width:48px; height:32px; margin:6px 0px 10px 0; background:#1AD4A2;\"> <img src=\"".ABS_URL."img/twitter.png\" alt=\"twitter\" style=\"display:block; margin:0;\"> </a> <a href=\"https://www.instagram.com/alshuaib_financial/\" target=\"_blank\" style=\"float:left; width:48px; height:32px; margin:6px 0px 10px 0; background:#1AD4A2;\"> <img src=\"".ABS_URL."img/instagram.png\" alt=\"instagram\" style=\"display:block; margin:0;\"> </a> <a href=\"https://www.linkedin.com/company/al-shuaib-international-financial-brokerage\" target=\"_blank\" style=\"float:left; width:48px; height:32px; margin:6px 0px 10px 0; background:#1AD4A2;\"> <img src=\"".ABS_URL."img/linkedin.png\" alt=\"linkedin\" style=\"display:block; margin:0;\"> </a> </p></div></td></tr><tr> <td style=\"padding-left:23px; padding-right:23px;\"> <p style=\"text-align:justify; margin:14px 0 14px; font-size:10px; line-height:14px; color:#ffffff;\"> Risk Warning: Trading Forex / CFD's on margin carries a high level of risk, is subject to rapid and unexpected price movements, and may not be suitable as you could sustain a total loss of your deposit. Please ensure that you understand the risks involved. Al-Shuaib International Financial Brokerage Co. is authorized and regulated by the Ministry of Trade and Industry as a Financial and Monetary Intermediary </p><div class=\"space-32\"></div><p style=\"text-align:justify; margin:14px 0 14px; font-size:12px; line-height:18px; color:#ffffff; \"> Copyright &copy; ". date('Y')." al shuaib international financial brokerage co. </p></td></tr><tr> <td> <p style=\" text-align:justify; margin:0; font-size:1px; line-height:1px;\">&nbsp;</p></td></tr></tbody> </table> </td></tr></tbody> </table> </body></html>";
    }

    private static function getUserHead($name) {
        return '<p class=""><strong>Dear '.$name.',</strong></p><table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
    }

    private static function getUserFoot() {
        return '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table><p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com">info@al-shuaib.com</a> or call us at <a href="tel:+96522435501">+965-22435501</a></p><table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table><p>Best regards</p><table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table><p><strong>Al Shuaib International Financial Brokerage Co.</strong></p>';
    }
}

?>