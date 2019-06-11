<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace WHMCS\Module\Addon\AddonXtreamresell\Admin;

/**
 * Sample Admin Area Controller
 */
class Controller
{
    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function index($vars)
    {
        $modulelink = $vars["modulelink"];
        $version = $vars["version"];
        $LANG = $vars["_lang"];
        $license = $vars["license"];
        $script = file_get_contents("https://xtreamresell.com/install/version.txt");
        define("REMOTE_VERSION", $script);
        return "<p>Current license key: <strong>" . $license . "</strong></p>\n<p>The currently installed version is: <strong>" . $version . "</strong></p>\n<p>Remote version is: <strong>" . $script . "</strong></p>\n";
    }
    /**
     * Show action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function show($vars)
    {
        $modulelink = $vars["modulelink"];
        $version = $vars["version"];
        $LANG = $vars["_lang"];
        $configTextField = $vars["Text Field Name"];
        $configPasswordField = $vars["Password Field Name"];
        $configCheckboxField = $vars["Checkbox Field Name"];
        $configDropdownField = $vars["Dropdown Field Name"];
        $configRadioField = $vars["Radio Field Name"];
        $configTextareaField = $vars["Textarea Field Name"];
        return "\n<h2>Show</h2>\n\n<p>This is the <em>show</em> action output of the sample addon module.</p>\n\n<p>The currently installed version is: <strong>" . $version . "</strong></p>\n\n<p>\n    <a href=\"" . $modulelink . "\" class=\"btn btn-info\">\n        <i class=\"fa fa-arrow-left\"></i>\n        Back to home\n    </a>\n</p>\n";
    }
}

?>