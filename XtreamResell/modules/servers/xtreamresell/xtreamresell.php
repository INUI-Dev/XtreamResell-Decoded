<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

$dbkeys = Illuminate\Database\Capsule\Manager::table("tbladdonmodules")->where("module", "addonxtreamresell")->get();
$dbr = json_encode($dbkeys);
$dbr = json_decode($dbr, true);
foreach ($dbr as $row => $setting) {
    $localkey = $dbr[0]["value"];
    $licensekey = $dbr[3]["value"];
}
$results = xtreamresell_check_license($licensekey, $localkey);
if ($results["status"] == "Active") {
    if ($results["localkey"]) {
        $localkeydata = $results["localkey"];
        $data = Illuminate\Database\Capsule\Manager::table("tbladdonmodules")->where("module", "addonxtreamresell")->where("setting", "localkey")->update(array("value" => (string) $localkeydata));
    }
} else {
    if ($results["status"] == "Invalid") {
        echo "Please report this to our customer support: XtreamResell Module License key is Invalid" . "<br></br>";
    } else {
        if ($results["status"] == "Expired") {
            echo "Please report this to our customer support: XtreamResell Module License key is Expired" . "<br></br>";
        } else {
            if ($results["status"] == "Suspended") {
                echo "Please report this to our customer support: XtreamResell Module License key is Suspended" . "<br></br>";
            }
        }
    }
}
if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function xtreamresell_check_license($licensekey, $localkey = "")
{
    $whmcsurl = "https://xtreamresell.com/";
    $licensing_secret_key = "4e59e80b5ed21c79b3067a70bbdbc60e";
    $localkeydays = 10;
    $allowcheckfaildays = 5;
    $check_token = time() . md5(mt_rand(1000000000, 9999999999.0) . $licensekey);
    $checkdate = date("Ymd");
    $domain = $_SERVER["SERVER_NAME"];
    $usersip = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : $_SERVER["LOCAL_ADDR"];
    $dirpath = dirname(__FILE__);
    $verifyfilepath = "modules/servers/licensing/verify.php";
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n", "", $localkey);
        $localdata = substr($localkey, 0, strlen($localkey) - 32);
        $md5hash = substr($localkey, strlen($localkey) - 32);
        if ($md5hash == md5($localdata . $licensing_secret_key)) {
            $localdata = strrev($localdata);
            $md5hash = substr($localdata, 0, 32);
            $localdata = substr($localdata, 32);
            $localdata = base64_decode($localdata);
            $localkeyresults = unserialize($localdata);
            $originalcheckdate = $localkeyresults["checkdate"];
            if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                if ($localexpiry < $originalcheckdate) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    $validdomains = explode(",", $results["validdomain"]);
                    if (!in_array($_SERVER["SERVER_NAME"], $validdomains)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    $validips = explode(",", $results["validip"]);
                    if (!in_array($usersip, $validips)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    $validdirs = explode(",", $results["validdirectory"]);
                    if (!in_array($dirpath, $validdirs)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $postfields = array("licensekey" => $licensekey, "domain" => $domain, "ip" => $usersip, "dir" => $dirpath);
        if ($check_token) {
            $postfields["check_token"] = $check_token;
        }
        $query_string = "";
        foreach ($postfields as $k => $v) {
            $query_string .= $k . "=" . urlencode($v) . "&";
        }
        if (function_exists("curl_exec")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            $fp = fsockopen($whmcsurl, 80, $errno, $errstr, 5);
            if ($fp) {
                $newlinefeed = "\r\n";
                $header = "POST " . $whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
                $header .= "Host: " . $whmcsurl . $newlinefeed;
                $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
                $header .= "Content-length: " . @strlen($query_string) . $newlinefeed;
                $header .= "Connection: close" . $newlinefeed . $newlinefeed;
                $header .= $query_string;
                $data = "";
                @stream_set_timeout($fp, 20);
                @fputs($fp, $header);
                $status = @socket_get_status($fp);
                while (!@feof($fp) && $status) {
                    $data .= @fgets($fp, 1024);
                    $status = @socket_get_status($fp);
                }
                @fclose($fp);
            }
        }
        if (!$data) {
            $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
            if ($localexpiry < $originalcheckdate) {
                $results = $localkeyresults;
            } else {
                $results = array();
                $results["status"] = "Invalid";
                $results["description"] = "Remote Check Failed";
                return $results;
            }
        } else {
            preg_match_all("/<(.*?)>([^<]+)<\\/\\1>/i", $data, $matches);
            $results = array();
            foreach ($matches[1] as $k => $v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if (!is_array($results)) {
            exit("Invalid License Server Response");
        }
        if ($results["md5hash"] && $results["md5hash"] != md5($licensing_secret_key . $check_token)) {
            $results["status"] = "Invalid";
            $results["description"] = "MD5 Checksum Verification Failed";
            return $results;
        }
        if ($results["status"] == "Active") {
            $results["checkdate"] = $checkdate;
            $data_encoded = serialize($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
            $results["localkey"] = $data_encoded;
        }
        $results["remotecheck"] = true;
    }
    unset($postfields);
    unset($data);
    unset($matches);
    unset($whmcsurl);
    unset($licensing_secret_key);
    unset($checkdate);
    unset($usersip);
    unset($localkeydays);
    unset($allowcheckfaildays);
    unset($md5hash);
    return $results;
}
/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function xtreamresell_MetaData()
{
    return array("DisplayName" => "XtreamResell", "APIVersion" => "1.2", "RequiresServer" => false, "DefaultNonSSLPort" => "7999", "DefaultSSLPort" => "1112", "ServiceSingleSignOnLabel" => "Login to Panel as User", "AdminSingleSignOnLabel" => "Login to Panel as Admin");
}
/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
 */
function xtreamresell_ConfigOptions()
{
    return array("Reseller Username" => array("Type" => "text", "Size" => "20", "Default" => "", "Description" => "", "SimpleMode" => true), "Reseller Password" => array("Type" => "password", "Size" => "20", "Default" => "", "Description" => "", "SimpleMode" => true), "XtreamCodes URL" => array("Type" => "text", "Size" => "40", "Default" => "", "Description" => "Include / on the end", "SimpleMode" => true), "Reseller Domain URL" => array("Type" => "text", "Size" => "40", "Default" => "", "Description" => "Include / on the end", "SimpleMode" => true), "Line Type" => array("Type" => "dropdown", "Options" => "trial,official", "Description" => "System Use/Backward compatibility", "SimpleMode" => false), "Trial ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "Monthly ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "Quarterly ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "Semi-Annually ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "Annually ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "PPV ID" => array("Type" => "dropdown", "Size" => "25", "Loader" => "xtreamresell_LoaderFunction1", "SimpleMode" => true), "Reseller Credits" => array("Type" => "yesno", "Description" => "Enable if product is for reseller Credits", "SimpleMode" => true), "Credit Amount" => array("Type" => "text", "Size" => "10", "Default" => "", "Description" => "Total Credits to be Credited", "SimpleMode" => true), "Captcha Required" => array("Type" => "yesno", "Description" => "Enable if Site is protected by a Captcha", "SimpleMode" => true), "Captcha API Key" => array("Type" => "text", "Size" => "40", "Default" => "", "Description" => "Your Captcha API Key", "SimpleMode" => true), "G-Recaptcha Key" => array("Type" => "text", "Size" => "40", "Default" => "", "Description" => "Xtreamcodes Website G-Recaptcha Key", "SimpleMode" => true), "Get" => array("Type" => "yesno", "Description" => "Get Server Contents", "SimpleMode" => true), "Subportal" => array("Type" => "text", "Size" => "40", "Default" => "", "Description" => "Subportal script brand name", "SimpleMode" => true));
}
function getAdminUserName()
{
    $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
    if (!empty($adminData)) {
        return $adminData->username;
    }
    exit("No admin exist. Why So?");
}
function curlPost($url, $postData)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt", CURLOPT_FRESH_CONNECT => 1));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = mb_stripos($string, $start, 0, "UTF-8");
    if ($ini == 0) {
        return "";
    }
    $ini += mb_strlen($start, "UTF-8");
    $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
    return mb_substr($string, $ini, $len, "UTF-8");
}
function getAdminUserName()
{
    $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
    if (!empty($adminData)) {
        return $adminData->username;
    }
    exit("No admin exist. Why So?");
}
function curlPost($url, $postData)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt", CURLOPT_FRESH_CONNECT => 1));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = mb_stripos($string, $start, 0, "UTF-8");
    if ($ini == 0) {
        return "";
    }
    $ini += mb_strlen($start, "UTF-8");
    $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
    return mb_substr($string, $ini, $len, "UTF-8");
}
function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = mb_stripos($string, $start, 0, "UTF-8");
    if ($ini == 0) {
        return "";
    }
    $ini += mb_strlen($start, "UTF-8");
    $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
    return mb_substr($string, $ini, $len, "UTF-8");
}
function getAdminUserName()
{
    $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
    if (!empty($adminData)) {
        return $adminData->username;
    }
    exit("No admin exist. Why So?");
}
/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function xtreamresell_CreateAccount(array $params)
{
    try {
        $serviceid = $params["serviceid"];
        $reseller_login = $params["configoption1"];
        $reseller_pass = $params["configoption2"];
        $reseller_url = $params["configoption3"];
        $domain_url = $params["configoption4"];
        $line_type = $params["configoption5"];
        $trial_option_value = $params["configoption6"];
        $config_monthly = $params["configoption7"];
        $config_quarterly = $params["configoption8"];
        $config_semi = $params["configoption9"];
        $config_annually = $params["configoption10"];
        $config_ppv = $params["configoption11"];
        $reseller_line = $params["configoption12"];
        $reseller_credits = $params["configoption13"];
        $captcha_required = $params["configoption14"];
        $captcha_key = $params["configoption15"];
        $grecaptcha_key = $params["configoption16"];
        $get_once = $params["configoption17"];
        $subportal_id = $params["configoption18"];
        if ($reseller_line == "on") {
            $serviceusername = $params["customfields"]["Reseller"];
            $command = "UpdateClientProduct";
            $postData = array("serviceid" => (string) $serviceid, "serviceusername" => (string) $serviceusername, "domain" => (string) $domain_url);
            if (!function_exists("getAdminUserName")) {
                function getAdminUserName()
                {
                    $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
                    if (!empty($adminData)) {
                        return $adminData->username;
                    }
                    exit("No admin exist. Why So?");
                }
            }
            $adminUsername = getAdminUserName();
            $results = localAPI($command, $postData, $adminUsername);
        } else {
            $custom_username = $params["customfields"]["Username"];
            $custom_mac = $params["customfields"]["MAC"];
            if (empty($custom_username)) {
                $serviceusername = $params["customfields"]["MAC"];
                $serviceusername = strtoupper($serviceusername);
                if (!function_exists("curlPost")) {
                    function curlPost($url, $postData)
                    {
                        $ch = curl_init();
                        curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt", CURLOPT_FRESH_CONNECT => 1));
                        $output = curl_exec($ch);
                        curl_close($ch);
                        return $output;
                    }
                    if ($captcha_required == "on") {
                        $apiKey = $captcha_key;
                        $googleKey = $grecaptcha_key;
                        $pageUrl = $reseller_url . "index.php";
                        $retrieve = file_get_contents("http://2captcha.com/in.php?key=" . $apiKey . "&method=userrecaptcha&googlekey=" . $googleKey . "&pageurl=" . $pageUrl);
                        $first = array($retrieve);
                        $result = explode("OK|", $first[0]);
                        $hello = $result[1];
                        $con = "http://2captcha.com/res.php?key=" . $apiKey . "&action=get&id=" . $hello;
                        sleep(30);
                        $getting = file_get_contents($con);
                        $second = array($getting);
                        $secondresult = explode("OK|", $second[0]);
                        $recaptchaToken = $secondresult[1];
                    }
                }
                $postData = array("login" => (string) $reseller_login, "pass" => (string) $reseller_pass, "g-recaptcha-response" => (string) $recaptchaToken);
                $output = curlPost($reseller_url . "index.php?action=login", $postData);
                $postData = array();
                $output = curlPost($reseller_url . "userpanel/add_mag.php", $postData);
                if (!function_exists("get_string_between")) {
                    function get_string_between($string, $start, $end)
                    {
                        $string = " " . $string;
                        $ini = mb_stripos($string, $start, 0, "UTF-8");
                        if ($ini == 0) {
                            return "";
                        }
                        $ini += mb_strlen($start, "UTF-8");
                        $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
                        return mb_substr($string, $ini, $len, "UTF-8");
                    }
                }
                $fullstring = $output;
                $res = get_string_between($fullstring, "index.php?action=logout&csrf_token=", "\"><u>");
                $token = preg_replace("/[^a-zA-Z0-9]/", "", $res);
                $postData = array("mac" => (string) $serviceusername, "package_id" => (string) $trial_option_value, "line_type" => "trial", "reseller_notes" => "", "csrf_token" => (string) $token);
                $output = curlPost($reseller_url . "userpanel/add_mag.php?action=add_mag", $postData);
                $command = "UpdateClientProduct";
                $postData = array("serviceid" => (string) $serviceid, "serviceusername" => (string) $serviceusername, "domain" => (string) $domain_url);
                if (!function_exists("getAdminUserName")) {
                    function getAdminUserName()
                    {
                        $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
                        if (!empty($adminData)) {
                            return $adminData->username;
                        }
                        exit("No admin exist. Why So?");
                    }
                }
                $adminUsername = getAdminUserName();
                $results = localAPI($command, $postData, $adminUsername);
            } else {
                $serviceusername = $params["customfields"]["Username"];
                $serviceusername = strtolower($serviceusername);
                if (!function_exists("curlPost")) {
                    function curlPost($url, $postData)
                    {
                        $ch = curl_init();
                        curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt", CURLOPT_FRESH_CONNECT => 1));
                        $output = curl_exec($ch);
                        curl_close($ch);
                        return $output;
                    }
                    if ($captcha_required == "on") {
                        $apiKey = $captcha_key;
                        $googleKey = $grecaptcha_key;
                        $pageUrl = $reseller_url . "index.php";
                        $retrieve = file_get_contents("http://2captcha.com/in.php?key=" . $apiKey . "&method=userrecaptcha&googlekey=" . $googleKey . "&pageurl=" . $pageUrl);
                        $first = array($retrieve);
                        $result = explode("OK|", $first[0]);
                        $hello = $result[1];
                        $con = "http://2captcha.com/res.php?key=" . $apiKey . "&action=get&id=" . $hello;
                        sleep(30);
                        $getting = file_get_contents($con);
                        $second = array($getting);
                        $secondresult = explode("OK|", $second[0]);
                        $recaptchaToken = $secondresult[1];
                    }
                }
                $postData = array("login" => (string) $reseller_login, "pass" => (string) $reseller_pass, "g-recaptcha-response" => (string) $recaptchaToken);
                $output = curlPost($reseller_url . "index.php?action=login", $postData);
                $postData = array();
                $output = curlPost($reseller_url . "userpanel/add_user.php", $postData);
                if (!function_exists("get_string_between")) {
                    function get_string_between($string, $start, $end)
                    {
                        $string = " " . $string;
                        $ini = mb_stripos($string, $start, 0, "UTF-8");
                        if ($ini == 0) {
                            return "";
                        }
                        $ini += mb_strlen($start, "UTF-8");
                        $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
                        return mb_substr($string, $ini, $len, "UTF-8");
                    }
                }
                $fullstring = $output;
                $res = get_string_between($fullstring, "index.php?action=logout&csrf_token=", "\"><u>");
                $token = preg_replace("/[^a-zA-Z0-9]/", "", $res);
                $postData = array("username" => (string) $serviceusername, "password" => "", "package_id" => (string) $trial_option_value, "line_type" => "trial", "reseller_notes" => "", "csrf_token" => (string) $token);
                $output = curlPost($reseller_url . "userpanel/add_user.php?action=add_user", $postData);
                $postData = array();
                $output = curlPost($reseller_url . "userpanel/extend.php", $postData);
                if (!function_exists("get_string_between")) {
                    function get_string_between($string, $start, $end)
                    {
                        $string = " " . $string;
                        $ini = mb_stripos($string, $start, 0, "UTF-8");
                        if ($ini == 0) {
                            return "";
                        }
                        $ini += mb_strlen($start, "UTF-8");
                        $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
                        return mb_substr($string, $ini, $len, "UTF-8");
                    }
                }
                $fullstring = $output;
                $genpass = get_string_between($fullstring, "" . (string) $serviceusername . " || Pass: ", "</option>");
                $genpass = substr($genpass, 0, 15);
                $command = "UpdateClientProduct";
                $postData = array("serviceid" => (string) $serviceid, "serviceusername" => (string) $serviceusername, "servicepassword" => (string) $genpass, "domain" => (string) $domain_url);
                if (!function_exists("getAdminUserName")) {
                    function getAdminUserName()
                    {
                        $adminData = Illuminate\Database\Capsule\Manager::table("tbladmins")->where("disabled", "=", 0)->first();
                        if (!empty($adminData)) {
                            return $adminData->username;
                        }
                        exit("No admin exist. Why So?");
                    }
                }
                $adminUsername = getAdminUserName();
                $results = localAPI($command, $postData, $adminUsername);
            }
        }
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_CreateAccount", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
function curlPost($url, $postData)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookieloader.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookieloader.txt", CURLOPT_FRESH_CONNECT => 1));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = mb_strpos($string, $start, 0, "UTF-8");
    if ($ini == 0) {
        return "";
    }
    $ini += mb_strlen($start, "UTF-8");
    $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
    return mb_substr($string, $ini, $len, "UTF-8");
}
/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function xtreamresell_LoaderFunction1()
{
    if (!isset($_POST["id"])) {
        $data = Illuminate\Database\Capsule\Manager::table("tblproducts")->where("servertype", "xtreamresell")->where("id", $_GET["id"])->get();
    } else {
        $data = Illuminate\Database\Capsule\Manager::table("tblproducts")->where("servertype", "xtreamresell")->where("id", $_POST["id"])->get();
    }
    $packageconfigoption = array();
    if (is_array($data) && 0 < count($data)) {
        $packageconfigoption[1] = $data[0]->configoption1;
        $packageconfigoption[2] = $data[0]->configoption2;
        $packageconfigoption[3] = $data[0]->configoption3;
        $packageconfigoption[14] = $data[0]->configoption14;
        $packageconfigoption[15] = $data[0]->configoption15;
        $packageconfigoption[16] = $data[0]->configoption16;
        $packageconfigoption[17] = $data[0]->configoption17;
        $packageconfigoption[25] = $data[0]->id;
    }
    list(, $reseller_login, $reseller_pass, $reseller_url, , , , , , , , , , , $captcha_required, $captcha_key, $grecaptcha_key, $get_once, , , , , , , , $id) = $packageconfigoption;
    if ($get_once == "on") {
        $data = Illuminate\Database\Capsule\Manager::table("tblproducts")->where("id", (string) $id)->update(array("configoption17" => ""));
        $apiKey = $captcha_key;
        $googleKey = $grecaptcha_key;
        $pageUrl = $reseller_url . "index.php";
        $retrieve = file_get_contents("http://2captcha.com/in.php?key=" . $apiKey . "&method=userrecaptcha&googlekey=" . $googleKey . "&pageurl=" . $pageUrl);
        $first = array($retrieve);
        $result = explode("OK|", $first[0]);
        $hello = $result[1];
        $con = "http://2captcha.com/res.php?key=" . $apiKey . "&action=get&id=" . $hello;
        sleep(30);
        $getting = file_get_contents($con);
        $second = array($getting);
        $secondresult = explode("OK|", $second[0]);
        $recaptchaToken = $secondresult[1];
        $data = Illuminate\Database\Capsule\Manager::table("tblproducts")->where("id", (string) $id)->update(array("configoption22" => (string) $recaptchaToken));
    } else {
        $recaptchaToken = Illuminate\Database\Capsule\Manager::table("tblproducts")->where("id", (string) $id)->value("configoption22");
        if (!function_exists("curlPost")) {
            function curlPost($url, $postData)
            {
                $ch = curl_init();
                curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt", CURLOPT_FRESH_CONNECT => 1));
                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
            }
        }
        $postData = array("login" => (string) $reseller_login, "pass" => (string) $reseller_pass, "g-recaptcha-response" => (string) $recaptchaToken);
        $output = curlPost($reseller_url . "index.php?action=login", $postData);
        $postData = array();
        $output = curlPost($reseller_url . "userpanel/add_user.php", $postData);
        if (!function_exists("get_string_between")) {
            function get_string_between($string, $start, $end)
            {
                $string = " " . $string;
                $ini = mb_stripos($string, $start, 0, "UTF-8");
                if ($ini == 0) {
                    return "";
                }
                $ini += mb_strlen($start, "UTF-8");
                $len = mb_strpos($string, $end, $ini, "UTF-8") - $ini;
                return mb_substr($string, $ini, $len, "UTF-8");
            }
        }
        $fullstring = $output;
        $res = get_string_between($fullstring, "</td>\n<td>", "</select>");
        preg_match_all("@(<option value=\"([^\"]+)\">([^<]+)<\\/option>)@", $res, $arr);
        $result = array();
        array_push($result, "None");
        foreach ($arr[0] as $i => $value) {
            $result[$arr[2][$i]] = $arr[3][$i];
        }
        $packageNames = $result;
        if (is_null($packageNames)) {
            throw new Exception("Invalid response format");
        }
        $list = array();
        foreach ($packageNames as $packageName => $packageID) {
            $list[$packageName] = ucfirst($packageID);
        }
        return $list;
    }
}
function xtreamresell_SuspendAccount(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_SuspendAccount", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function xtreamresell_UnsuspendAccount(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_UnsuspendAccount", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function xtreamresell_TerminateAccount(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_TerminateAccount", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function xtreamresell_ChangePackage(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_ChangePackage", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function xtreamresell_TestConnection(array $params)
{
    try {
        $success = true;
        $errorMsg = "";
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_TestConnection", $params, $e->getMessage(), $e->getTraceAsString());
        $success = false;
        $errorMsg = $e->getMessage();
    }
    return array("success" => $success, "error" => $errorMsg);
}
/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see xtreamresell_buttonOneFunction()
 *
 * @return array
 */
function xtreamresell_ClientAreaCustomButtonArray()
{
    return array("Login Details" => "actionOneFunction");
}
/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see xtreamresell_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function xtreamresell_buttonOneFunction(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_buttonOneFunction", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see xtreamresell_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function xtreamresell_actionOneFunction(array $params)
{
    try {
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_actionOneFunction", $params, $e->getMessage(), $e->getTraceAsString());
        return $e->getMessage();
    }
    return "success";
}
/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see xtreamresell_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function xtreamresell_AdminServicesTabFieldsSave(array $params)
{
    $originalFieldValue = isset($_REQUEST["xtreamresell_original_uniquefieldname"]) ? $_REQUEST["xtreamresell_original_uniquefieldname"] : "";
    $newFieldValue = isset($_REQUEST["xtreamresell_uniquefieldname"]) ? $_REQUEST["xtreamresell_uniquefieldname"] : "";
    if ($originalFieldValue != $newFieldValue) {
        try {
        } catch (Exception $e) {
            logModuleCall("xtreamresell", "xtreamresell_AdminServicesTabFieldsSave", $params, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function xtreamresell_ServiceSingleSignOn(array $params)
{
    try {
        $response = array();
        return array("success" => true, "redirectTo" => $response["redirectUrl"]);
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_ServiceSingleSignOn", $params, $e->getMessage(), $e->getTraceAsString());
        return array("success" => false, "errorMsg" => $e->getMessage());
    }
}
/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function xtreamresell_AdminSingleSignOn(array $params)
{
    try {
        $response = array();
        return array("success" => true, "redirectTo" => $response["redirectUrl"]);
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_AdminSingleSignOn", $params, $e->getMessage(), $e->getTraceAsString());
        return array("success" => false, "errorMsg" => $e->getMessage());
    }
}
/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function xtreamresell_WHMCSReconnect()
{
    require ROOTDIR . "/configuration.php";
    $whmcsmysql = mysqli_connect($db_host, $db_username, $db_password);
    mysqli_select_db($db_name);
}
function xtreamresell_ClientArea(array $params)
{
    $requestedAction = isset($_REQUEST["customAction"]) ? $_REQUEST["customAction"] : "";
    if ($requestedAction == "manage") {
        $serviceAction = "get_usage";
        $templateFile = "templates/manage.tpl";
    } else {
        $serviceAction = "get_stats";
        $templateFile = "templates/overview.tpl";
    }
    try {
        $response = array();
        $extraVariable1 = "abc";
        $extraVariable2 = "123";
        return array("tabOverviewReplacementTemplate" => $templateFile, "templateVariables" => array("extraVariable1" => $extraVariable1, "extraVariable2" => $extraVariable2));
    } catch (Exception $e) {
        logModuleCall("xtreamresell", "xtreamresell_ClientArea", $params, $e->getMessage(), $e->getTraceAsString());
        return array("tabOverviewReplacementTemplate" => "error.tpl", "templateVariables" => array("usefulErrorHelper" => $e->getMessage()));
    }
}

?>