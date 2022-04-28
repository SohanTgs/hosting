<?php

use App\Lib\GoogleAuthenticator;
use App\Lib\SendSms;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\SmsTemplate;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Nullable;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


function sidebarVariation(){

    /// for sidebar
    $variation['sidebar'] = 'bg--black';
    $variation['sidebar'] = 'bg--white';
    $variation['sidebar'] = 'bg--15';
    $variation['sidebar'] = 'bg--dark';
    $variation['sidebar'] = 'bg--indigo';
    $variation['sidebar'] = 'bg--brown';
    $variation['sidebar'] = 'bg--1';
    $variation['sidebar'] = 'bg--3';
    $variation['sidebar'] = 'bg--10';
    $variation['sidebar'] = 'bg--11';
    $variation['sidebar'] = 'bg--12';
    $variation['sidebar'] = 'bg--13';
    $variation['sidebar'] = 'bg--15';
    $variation['sidebar'] = 'bg--17';
    $variation['sidebar'] = 'bg--19';
    $variation['sidebar'] = 'bg--gradi-1';
    $variation['sidebar'] = 'bg--gradi-17';
    $variation['sidebar'] = 'bg--gradi-19';
    $variation['sidebar'] = 'bg--gradi-20';
    $variation['sidebar'] = 'bg--gradi-24';
    $variation['sidebar'] = 'bg--gradi-32';
    $variation['sidebar'] = 'bg--gradi-50';
    $variation['sidebar'] = 'bg--gradi-49';
    $variation['sidebar'] = 'bg--gradi-12';
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded2';
    $variation['selector'] = 'capsule--rounded';

    //for overlay
    $variation['overlay'] = 'overlay--gradi-1'; // 1-50
    $variation['overlay'] = 'none';
    $variation['overlay'] = 'overlay--indigo'; //For more, visit here http://fahad.thesoftking.com/tsk/tskadmin/docs-overlay.html

    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;

}

function systemDetails()
{
    $system['name'] = 'Laramin';
    $system['version'] = '3.1.51';
    return $system;
}

function getLatestVersion()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/version/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
} 


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}


function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}


function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}


function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



//moveable
function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $image = Image::make($file);
    if ($size) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);

    if ($thumb) {
        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null){
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location,$filename);
    return $filename;
}

function makeDirectory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}


function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}


function activeTemplate($asset = false)
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    return $template;
}


function loadReCaptcha()
{
    $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}


function loadAnalytics()
{
    $analytics = Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function loadTawkto()
{
    $tawkto = Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}


function loadFbComment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status',1)->first();
    return  $comment ? $comment->generateScript() : '';
}

function loadCustomCaptcha($height = 46, $width = '300px', $bgcolor = '#003', $textcolor = '#abc')
{
    $textcolor = '#'.GeneralSetting::first()->base_color;
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    if (!$captcha) {
        return 0;
    }
    $code = rand(100000, 999999);
    $char = str_split($code);
    $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
    $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
    foreach ($char as $value) {
        $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
    }
    $ret .= '</div>';
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
    return $ret;
}


function captchaVerify($code, $secret)
{
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    if ($captchaSecret == $secret) {
        return true;
    }
    return false;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
    $separator = '';
    if($separate){
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if($exceptZeros){
    $exp = explode('.', $printAmount);
        if($exp[1]*1 == 0){
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{

    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

//moveable
function curlContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//moveable
function curlPostContent($url, $arr = null)
{
    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
function getIpInfo()
{
    $ip = $_SERVER["REMOTE_ADDR"];

    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }


    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = request()->ip();
    $data['time'] = date('d-m-Y h:i:s A');


    return $data;
}

//moveable
function osBrowser(){
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $osPlatform = "Unknown OS Platform";
    $osArray = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );
    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $osPlatform = $value;
        }
    }
    $browser = "Unknown Browser";
    $browserArray = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );
    foreach ($browserArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $browser = $value;
        }
    }

    $data['os_platform'] = $osPlatform;
    $data['browser'] = $browser;

    return $data;
}

function siteName()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}


//moveable
function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image,$size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image',$size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $type, $shortCodes = null)
{

    sendEmail($user, $type, $shortCodes);
    sendSms($user, $type, $shortCodes);
}



function sendSms($user, $type, $shortCodes = [])
{
    $general = GeneralSetting::first();
    $smsTemplate = SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    $gateway = $general->sms_config->name;
    $sendSms = new SendSms;
    if ($general->sn == 1 && $smsTemplate) {
        $template = $smsTemplate->sms_body;
        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }
        $message = shortCodeReplacer("{{message}}", $template, $general->sms_api);
        $message = shortCodeReplacer("{{name}}", $user->username, $message);
        $sendSms->$gateway($user->mobile,$general->sitename,$message,$general->sms_config);
    }
}

function sendEmail($user, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();

    $emailTemplate = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$emailTemplate) {
        return;
    }


    $message = shortCodeReplacer("{{fullname}}", $user->fullname, $general->email_template);
    $message = shortCodeReplacer("{{username}}", $user->username, $message);
    $message = shortCodeReplacer("{{message}}", $emailTemplate->email_body, $message);

    if (empty($message)) {
        $message = $emailTemplate->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }

    $config = $general->mail_config;

    $emailLog = new EmailLog();
    $emailLog->user_id = $user->id;
    $emailLog->mail_sender = $config->name;
    $emailLog->email_from = $general->sitename.' '.$general->email_from;
    $emailLog->email_to = $user->email;
    $emailLog->subject = $emailTemplate->subj;
    $emailLog->message = $message;
    $emailLog->save();


    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$emailTemplate->subj, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    }
}


function sendPhpMail($receiver_email, $receiver_name, $subject, $message,$general)
{
    $headers = "From: $general->sitename <$general->email_from> \r\n";
    $headers .= "Reply-To: $general->sitename <$general->email_from> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    mail($receiver_email, $subject, $message, $headers);
}


function sendSmtpMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $config->username;
        $mail->Password   = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($general->email_from, $general->sitename);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($general->email_from, $general->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e); 
    }
}
 

function sendSendGridMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($general->email_from, $general->sitename);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        throw new Exception($e); 
    }
}


function sendMailjetMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $general->email_from,
                    'Name' => $general->sitename,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function getPaginate($paginate = 20)
{  
    return $paginate;
}

function paginateLinks($data, $design = 'admin.partials.paginate'){
    return $data->appends(request()->all())->links($design);
}


function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}


function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw'=>[
            'path'=>'assets/images/verify/withdraw'
        ],
        'deposit'=>[
            'path'=>'assets/images/verify/deposit'
        ]
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ]
    ];
    $data['ticket'] = [
        'path' => 'assets/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64'
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
        'size' => '36x36',
    ];
    $data['helps'] = [
        'path' => 'assets/images/helps'
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315'
    ];
    $data['profile'] = [
        'user'=> [
            'path'=>'assets/images/user/profile',
            'size'=>'350x300'
        ],
        'admin'=> [
            'path'=>'assets/admin/images/profile',
            'size'=>'400x400'
        ]
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    
    if($date){
        return Carbon::parse($date)->translatedFormat($format);
    }

    return false;
}

//moveable
function sendGeneralEmail($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();


    if ($general->en != 1 || !$general->email_from) {
        return;
    }


    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", $receiver_name, $message);
    $message = shortCodeReplacer("{{username}}", $email, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$subject, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$subject, $message, $general);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null,$orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->orderBy('id','desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if($orderById){
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        }else{
            $content = $article->where('data_keys', $data_keys)->orderBy('id','desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false){
    if ($type) {
        return 'user.deposit.history';
    }else{
        return 'user.deposit';
    }
}

function verifyG2fa($user,$code,$secret = null)
{
    $ga = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $ga->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}

 
function urlPath($routeName,$routeParam=null){
    if($routeParam == null){
        $url = route($routeName);
    } else {
        $url = route($routeName,$routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath,'',$url);
    return $path;
}


function productType(){
    $array = [
        1 => 'Shared Hosting',
        2 => 'Reseller Hosting',
        3 => 'Server/VPS',
        4 => 'Other',
    ];

    return $array;
} 

function welcomeEmail(){
    try{
        
        $array = [
            1 => ['name'=>'Hosting Account Welcome Email', 'act'=>'HOSTING_ACCOUNT'],
            2 => ['name'=>'Reseller Account Welcome Email', 'act'=>'RESELLER_ACCOUNT'],
            3 => ['name'=>'Dedicated/VPS Server Welcome Email', 'act'=>'VPS_SERVER'],
            4 => ['name'=>'Other Product/Service Welcome Email', 'act'=>'OTHER_PRODUCT'],
        ];
    
        return $array;

    }catch(\Exception $e){
        return $e->getMessage();
    } 
}

function productModule(){
    $array = [
        0 => 'None',
        1 => 'cPanel',
    ];

    return $array;
}

function productModuleOptions(){
    $array = [
        1 => 'Automatically setup the product as soon as an order is placed',
        2 => 'Automatically setup the product when you manually accept a pending order',
        3 => 'Do not automatically setup this product',
    ];

    return $array;
}
 
function pricing($billingType = null, $price = null, $type = null, $showText = false, $column = null){
    try{
       
        $array = [
            1 => ['setupFee'=>'monthly_setup_fee', 'price'=>'monthly'],
            2 => ['setupFee'=>'quarterly_setup_fee', 'price'=>'quarterly'],
            3 => ['setupFee'=>'semi_annually_setup_fee', 'price'=>'semi_annually'],
            4 => ['setupFee'=>'annually_setup_fee',  'price'=>'annually'],
            5 => ['setupFee'=>'biennially_setup_fee', 'price'=>'biennially'],
            6 => ['setupFee'=>'triennially_setup_fee', 'price'=>'triennially']
        ];
   
        if(!$price){ 
            return implode(',' , array_column($array, 'price'));
        }
        
        if(!$type){
            $general = GeneralSetting::first();
            $options = null; 

            foreach($array as $data){  
                $getColumn = $data['price']; 
                $getFeeColumn = $data['setupFee']; 
                $setupFee = null;

                if($billingType && $billingType == 1){
                    if($price->monthly_setup_fee > 0){
                        $setupFee .= ' + '.$general->cur_sym.getAmount($price->monthly_setup_fee).' '.$general->cur_text.' Setup Fee';
                    }
 
                    $options .= '<option value="monthly">'.
                                    $general->cur_sym.getAmount($price->monthly).' '.$general->cur_text.
                                    $setupFee
                                .'</option>';

                    return $options;
                }

                if($price->$getColumn >= 0){

                    if($price->$getFeeColumn > 0){
                        $setupFee .= ' + '.$general->cur_sym.getAmount($price->$getFeeColumn).' '.$general->cur_text.' Setup Fee';
                    }
 
                    $options .= '<option value="'. $getColumn .'">'.
                                    $general->cur_sym.getAmount($price->$getColumn).' '.$general->cur_text.' '.ucwords(str_replace('_', ' ', $getColumn)).' '.
                                    $setupFee
                                .'</option>';
                }

            }

            return $options;
        } 

        foreach($array as $data){ 
       
            $getColumn = $data['price'];  
 
            if($column){ 
                if($type == 'price'){
                    return getAmount($price->$column);
                }else{
                    $column = $column.'_setup_fee';
                    return getAmount($price->$column); 
                }
            }

            if($billingType && $billingType == 1){
                if($showText){
                    if($type == 'price'){ 
                        return 'One Time';
                    }
                    return 'Setup Fee';
                }
    
                if($type == 'price'){
                    return getAmount($price->monthly);
                }
    
                return getAmount($price->monthly_setup_fee);
            }

            if($price->$getColumn >= 0){
                
                if($showText){
                    if($type == 'price'){ 
                        $replace = str_replace('_', ' ', $getColumn);
                        return ucwords($replace);
                    }
                    return 'Setup Fee';
                }

                if($type == 'price'){
                    return getAmount($price->$getColumn);
                }

                $getColumn = $data[$type]; 
                return getAmount($price->$getColumn); 
            }
            
        }

    }catch(\Exception $e){
        return $e->getMessage();
    } 
}

function shoppingCart($product = null, $request = null, $deleteId = null, $billingType = null, $array = [], $domainData = null){
    
    $cart = session()->get('shoppingCart') ?? [];

    if($product == 'get'){
        return $cart;
    }
  
    if(!$product && !$request && !$deleteId){
        if(session()->has('shoppingCart')){ 
            return true; 
        }
    } 
 
    $product_id = $request->product_id ?? $deleteId;
    $index = array_search($product_id, array_column($cart, 'product_id'));

    if($deleteId){ 

        foreach($cart as $arrayIndex => $singleCart){
            if($singleCart['product_id'] == $deleteId && $singleCart['billing_type'] == $billingType){
                unset($cart[$arrayIndex]);
                $cart = array_reverse($cart); 
                return session()->put('shoppingCart', $cart);
            }
        }
     
    }
  
    session()->forget('coupon');
  
    if($request && !$deleteId){

        $domain = '';
        if($request->domain){
            $domain = $request->domain;
        }elseif($request->hostname){
            $domain = $request->hostname;
        }

        $new = [
            'product_id'=> $request->product_id,
            'name'=> $product->name,
            'category'=> $product->serviceCategory->name,

            'domain'=> $domain,
            'domain_id'=> $request->domain_id,

            'password'=> $request->password ?? null,
            'ns1'=> $request->ns1 ?? null,
            'ns2'=> $request->ns2 ?? null,

            'price'=> $array['price'],
            'setupFee'=> $array['setupFee'],
            'discount'=> 0,
            'total'=> $array['price'] + $array['setupFee'],
            'afterDiscount'=> $array['price'] + $array['setupFee'],

            'billing_cycle'=> $product->payment_type == 1 ? 1 : 2,

            'billing_type'=> $request->billing_type,
            'config_options'=> array_filter((array) $request->config_options)
        ];

        if(gettype($index) != 'integer'){
            array_push($cart, $new); 
        }else{

            $found = false;
            $foundIndex = 0;
            
            foreach($cart as $arrayIndex => $singleCart){ 
                if($singleCart['product_id'] == $new['product_id'] && $singleCart['billing_type'] == $new['billing_type']){ 
                    $foundIndex = $arrayIndex;
                    $found = true;
                }
            }

            if($found){ 
                $cart[$foundIndex]['price'] = $array['price'];

                $cart[$foundIndex]['domain'] = $domain;
                $cart[$foundIndex]['domain_id'] = $request->domain_id;

                $cart[$foundIndex]['password'] = $request->password ?? null;
                $cart[$foundIndex]['ns1'] = $request->ns1 ?? null;
                $cart[$foundIndex]['ns2'] = $request->ns2 ?? null;
                
                $cart[$foundIndex]['setupFee'] = $array['setupFee'];
                $cart[$foundIndex]['discount'] = 0;
                $cart[$foundIndex]['total'] = $array['price'] + $array['setupFee'];
                $cart[$foundIndex]['afterDiscount'] = $array['price'] + $array['setupFee'];

                $cart[$foundIndex]['billing_cycle'] = $product->payment_type == 1 ? 1 : 2;

                $cart[$foundIndex]['billing_type'] = $request->billing_type;
                $cart[$foundIndex]['config_options'] = array_filter((array) $request->config_options);
            }else{
              array_push($cart, $new);  
            }

        }
 
        foreach($cart as $index => $singleCart){ 
            $cart[$index]['discount'] = 0;
            $cart[$index]['afterDiscount'] = $cart[$index]['total'];
        }
       
        if($request->domain_id){
            $response = domainRegister($domainData, $domain);

            foreach($cart as $arrayIndex => $singleCart){
                if($singleCart['domain_id'] == $domainData->id && $singleCart['domain'] == $domain && $singleCart['product_id'] == 0){
                    unset($cart[$arrayIndex]);
                }
            }

            array_push($cart, $response);
        }

        $cart = array_reverse($cart);  
        return session()->put('shoppingCart', $cart);
    }

 
}

function domainRegister($domainData, $domainName){

    $array = [
        'product_id'=> 0,
        'name'=> 'Domain Registration',

        'domain_id'=> $domainData->id,
        'domain'=> $domainName,
        'id_protection'=> 0,

        'reg_period'=>@$domainData->pricing->firstPrice['year'] ?? 0,

        'price'=> @$domainData->pricing->firstPrice['price'] ?? 0,
        'setupFee'=> 0,
        'discount'=> 0,
        'total'=> @$domainData->pricing->firstPrice['price'] ?? 0,
        'afterDiscount'=> @$domainData->pricing->firstPrice['price'] ?? 0
    ];

    return $array;
}

function billingCycle($period = null, $showNextDate = false){
    try{

        $array = [
            0 => ['billing_type'=>'one_time', 'showText'=>'One Time', 'carbon'=>null], 
            1 => ['billing_type'=>'monthly', 'carbon'=>Carbon::now()->addMonth()->toDateTimeString(), 'showText'=>'Monthly'], 
            2 => ['billing_type'=>'quarterly', 'carbon'=>Carbon::now()->addMonth(3)->toDateTimeString(), 'showText'=>'Quarterly'], 
            3 => ['billing_type'=>'semi_annually', 'carbon'=>Carbon::now()->addMonth(6)->toDateTimeString(), 'showText'=>'Semi Annually'], 
            4 => ['billing_type'=>'annually', 'carbon'=>Carbon::now()->addYear()->toDateTimeString(), 'showText'=>'Annually'],
            5 => ['billing_type'=>'biennially', 'carbon'=>Carbon::now()->addYear(2)->toDateTimeString(), 'showText'=>'Biennially'], 
            6 => ['billing_type'=>'triennially', 'carbon'=>Carbon::now()->addYear(3)->toDateTimeString(), 'showText'=>'Triennially']
        ];
  
        if(!$period && !$showNextDate){
            return $array;
        }

        foreach($array as $index => $data){

            $type = $data['billing_type'];

            if(gettype($period) == 'integer'){
                $type = $index;
            }

            if($type == $period){
            
                if($showNextDate){
                    return $data;
                }
          
                return $index;
            }
        }

    }catch(\Exception $e){
        return $e->getMessage();
    } 
} 