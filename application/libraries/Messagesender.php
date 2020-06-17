<?php
include("Newsletterslk_SMS/newsletterslk.class.php");


class Messagesender
{


    public function send_otp($Mobile, $TEXT)
    {
        $newsletters = new Newsletterslk;
        $newsletters->setUser(APIKeys::SMS_API_KEY, APIKeys::SMS_API_TOKEN);// Initializing User Api Key and Api Token
        $newsletters->setSenderID(APIKeys::SMS_SENDER_ID);// Initializing Sender ID
        $newsletters->msgType = 'sms';
        $newsletters->file = '';            //Set to default
        $newsletters->language = '';        //Set to default
        $newsletters->scheduledate = '';    //Set to default
        $newsletters->duration = '';        //Set to default

        $TEXT = 'Your OTP code is : ' . $TEXT;

        if ($newsletters->SendMessage($Mobile, $TEXT, FALSE))
            return true;
        else
            return false;
    }


    public function send_otp_old($number, $msg)
    {

        $user = "94714102030";
        $password = "1923";
        $text = urlencode('MyNumber.lk OTP code : ' . $msg);
        $to = $number;

        $baseurl = "http://www.textit.biz/sendmsg";
        $url = "$baseurl/?id=$user&pw=$password&to=$to&text=$text";
        $ret = $this->get_web_page($url);
        $res = explode(":", $ret);


        if (trim($res[0]) == "OK") {
            return true;
        } else {
            return false;
        }
    }

    private function get_web_page($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_TIMEOUT => 500,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        return $content;
    }
}
