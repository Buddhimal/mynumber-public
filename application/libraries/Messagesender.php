<?php
include("Newsletterslk_SMS/newsletterslk.class.php");


class Messagesender
{

	public function send_sms($number, $msg)
	{
		$api_key = APIKeys::DIALOG_API_KEY;
		$sender_id = APIKeys::SMS_SENDER_ID;
		$msg = urlencode($msg);

		$baseurl = "https://cpsolutions.dialog.lk/index.php/cbs/sms/send";
		$url = "$baseurl/?destination=$number&q=$api_key&message=$msg&from=$sender_id";

		$ret = $this->call_url($url);

		if ($ret == 0) {
			return true;
		} else {
			return false;
		}
	}


	public function send_otp($number, $msg)
	{
		$api_key = APIKeys::DIALOG_API_KEY;
		$sender_id = APIKeys::SMS_SENDER_ID;
		$text = urlencode('Your OTP code is : ' . $msg);

		$baseurl = "https://cpsolutions.dialog.lk/index.php/cbs/sms/send";
		$url = "$baseurl/?destination=$number&q=$api_key&message=$text&from=$sender_id";

		$ret = $this->call_url($url);

		if ($ret == 0) {
			return true;
		} else {
			return false;
		}
	}


	private function call_url($url){

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "$url",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: multipart/form-data; boundary=--------------------------852740912331716884420101"
			),
		));

		$data = curl_exec($curl);

		return $data;
	}

    public function send_otp_new($Mobile, $TEXT)
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

	public function send_sms_new($Mobile, $TEXT)
	{
		$newsletters = new Newsletterslk;
		$newsletters->setUser(APIKeys::SMS_API_KEY, APIKeys::SMS_API_TOKEN);// Initializing User Api Key and Api Token
		$newsletters->setSenderID(APIKeys::SMS_SENDER_ID);// Initializing Sender ID
		$newsletters->msgType = 'sms';
		$newsletters->file = '';            //Set to default
		$newsletters->language = '';        //Set to default
		$newsletters->scheduledate = '';    //Set to default
		$newsletters->duration = '';        //Set to default

		if ($newsletters->SendMessage($Mobile, $TEXT, FALSE))
			return true;
		else
			return false;
	}

	public function send_sms_old($Mobile, $TEXT)
	{
		$newsletters = new Newsletterslk;
		$newsletters->setUser(APIKeys::SMS_API_KEY, APIKeys::SMS_API_TOKEN);// Initializing User Api Key and Api Token
		$newsletters->setSenderID(APIKeys::SMS_SENDER_ID);// Initializing Sender ID
		$newsletters->msgType = 'sms';
		$newsletters->file = '';            //Set to default
		$newsletters->language = '';        //Set to default
		$newsletters->scheduledate = '';    //Set to default
		$newsletters->duration = '';        //Set to default

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
