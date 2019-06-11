<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

add_hook("ClientEdit", 1, "hook_xtreamresell_clientedit");
add_hook("ClientAreaPrimaryNavbar", 1, function ($menu) {
    if (!is_null($menu->getChild("Services"))) {
        $menu->getChild("Services")->addChild("Provisioning Module Products", array("uri" => "clientarea.php?action=services&module=xtreamresell", "order" => 15));
    }
});
add_hook("InvoicePaid", 1, function ($vars) {
    $invoiceid = $vars["invoiceid"];
    $command = "GetInvoice";
    $postData = array("invoiceid" => $vars["invoiceid"]);
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
    for ($i = 0; $i < count($results["items"]["item"]); $i++) {
        $serviceid = $results["items"]["item"][(string) $i]["relid"];
        $type = $results["items"]["item"][(string) $i]["type"];
        if ($type != "Hosting") {
            echo "stop here";
        } else {
            $reseller_login = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption1");
            $reseller_pass = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption2");
            $reseller_url = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption3");
            $config_monthly = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption7");
            $config_quarterly = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption8");
            $config_semi = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption9");
            $config_annually = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption10");
            $config_ppv = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption11");
            $reseller_line = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption12");
            $reseller_credits = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption13");
            $captcha_required = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption14");
            $captcha_key = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption15");
            $grecaptcha_key = Illuminate\Database\Capsule\Manager::table("tblproducts")->join("tblhosting", "tblhosting.packageid", "=", "tblproducts.id")->where("tblhosting.id", "=", (string) $serviceid)->value("configoption16");
            if ($reseller_line == "on") {
                $serviceusername = Illuminate\Database\Capsule\Manager::table("tblhosting")->where("id", (string) $serviceid)->value("username");
                $serviceusername = preg_replace("/\\s+/", "", $serviceusername);
                $custom_reseller = $serviceusername;
                if (!function_exists("curlPost")) {
                    function curlPost($url, $postData)
                    {
                        $ch = curl_init();
                        curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookiehook.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookiehook.txt"));
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
                $output = curlPost($reseller_url . "userpanel/manage_subresellers.php", $postData);
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
                $postData = array("csrf_token" => (string) $token);
                $output = curlPost($reseller_url . "userpanel/manage_subresellers.php", $postData);
                $postData = array("cmd" => "get-records&limit=100&offset=0");
                $output = curlPost($reseller_url . "userpanel/manage_subresellers.php?action=load_users&csrf_token=" . $token, $postData);
                $res = preg_replace("/(.*)" . (string) $custom_reseller . "(.*)Remove Credits From " . (string) $custom_reseller . "(.*)/ism", "\\2", $output);
                $reseller_idraw = preg_replace("/(.*)credits&user_id=(.*)Reseller Add(.*)/ism", "\\2", $res);
                $reseller_id = preg_replace("/[^a-zA-Z0-9]/", "", $reseller_idraw);
                $output = curlPost($reseller_url . "userpanel/manage_subresellers.php", $postData);
                $postData = array("credits_num" => (string) $reseller_credits, "reason" => "added by whmcs");
                $output = curlPost($reseller_url . "userpanel/manage_subresellers.php?csrf_token=" . $token . "&action=credits&user_id=" . $reseller_id, $postData);
            } else {
                $serviceusername = Illuminate\Database\Capsule\Manager::table("tblhosting")->where("id", (string) $serviceid)->value("username");
                $serviceusername = preg_replace("/\\s+/", "", $serviceusername);
                $billingcycle = Illuminate\Database\Capsule\Manager::table("tblinvoiceitems")->join("tblhosting", "tblhosting.id", "=", "tblinvoiceitems.relid")->where("tblinvoiceitems.relid", "=", (string) $serviceid)->where("tblinvoiceitems.type", "=", "hosting")->where("tblinvoiceitems.invoiceid", "=", (string) $invoiceid)->value("billingcycle");
                if ($billingcycle == "Monthly") {
                    $package_id = $config_monthly;
                } else {
                    if ($billingcycle == "Quarterly") {
                        $package_id = $config_quarterly;
                    } else {
                        if ($billingcycle == "Semi-Annually") {
                            $package_id = $config_semi;
                        } else {
                            if ($billingcycle == "Annually") {
                                $package_id = $config_annually;
                            } else {
                                if ($billingcycle == "One Time") {
                                    $package_id = $config_ppv;
                                }
                            }
                        }
                    }
                }
                if (preg_match("/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}\$/", $serviceusername)) {
                    if (!function_exists("curlPost")) {
                        function curlPost($url, $postData)
                        {
                            $ch = curl_init();
                            curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookiehook.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookiehook.txt"));
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
                            sleep(35);
                            $getting = file_get_contents($con);
                            $second = array($getting);
                            $secondresult = explode("OK|", $second[0]);
                            $recaptchaToken = $secondresult[1];
                        }
                    }
                    $postData = array("login" => (string) $reseller_login, "pass" => (string) $reseller_pass, "g-recaptcha-response" => (string) $recaptchaToken);
                    $output = curlPost($reseller_url . "index.php?action=login", $postData);
                    $postData = array();
                    $output = curlPost($reseller_url . "userpanel/extend_mag.php", $postData);
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
                    $res = preg_replace("/(.*)user_id\" required>(.*)td>(.*)/ism", "\\2", $output);
                    $res1 = substr($res, 0, stripos($res, (string) $serviceusername));
                    $user_id = preg_replace("/(.*)<option value=\"(.*)\">(.*)/ism", "\\2", $res1);
                    $user_id = preg_replace("/[^a-zA-Z0-9]/", "", $user_id);
                    $postData = array("user_id" => (string) $user_id, "package_id" => (string) $package_id, "line_type" => "official", "reseller_notes" => "", "csrf_token" => (string) $token);
                    $output = curlPost($reseller_url . "userpanel/extend_mag.php?action=extend", $postData);
                } else {
                    if (!function_exists("curlPost")) {
                        function curlPost($url, $postData)
                        {
                            $ch = curl_init();
                            curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookiehook.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookiehook.txt"));
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
                    $res = get_string_between($fullstring, "index.php?action=logout&csrf_token=", "\"><u>");
                    $token = preg_replace("/[^a-zA-Z0-9]/", "", $res);
                    $res = preg_replace("/(.*)user_id\" required>(.*)td>(.*)/ism", "\\2", $output);
                    $res1 = substr($res, 0, stripos($res, (string) $serviceusername));
                    $user_id = preg_replace("/(.*)<option value=\"(.*)\">User:(.*)/ism", "\\2", $res1);
                    $user_id = preg_replace("/[^a-zA-Z0-9]/", "", $user_id);
                    $postData = array("user_id" => (string) $user_id, "package_id" => (string) $package_id, "line_type" => "official", "reseller_notes" => "", "csrf_token" => (string) $token);
                    $output = curlPost($reseller_url . "userpanel/extend.php?action=extend", $postData);
                    logActivity("Hook variables: " . print_r($vars, true));
                    error_log(print_r($vars, true), 3, __DIR__ . "/file.log");
                }
            }
        }
    }
});
echo "\n\n\n";
/**
 * Client edit sample hook function.
 *
 * This sample demonstrates making a service call whenever a change is made to a
 * client profile within WHMCS.
 *
 * @param array $params Parameters dependant upon hook function
 *
 * @return mixed Return dependant upon hook function
 */
function hook_xtreamresell_clientedit(array $params)
{
    try {
    } catch (Exception $e) {
    }
}
function curlPost($url, $postData)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt"));
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
function curlPost($url, $postData)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postData, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", CURLOPT_COOKIESESSION => true, CURLOPT_COOKIEFILE => dirname(__FILE__) . "/cookie.txt", CURLOPT_COOKIEJAR => dirname(__FILE__) . "/cookie.txt"));
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

?>