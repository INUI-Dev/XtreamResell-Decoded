<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
function AddonXtreamresell_config()
{
    return array("name" => "XtreamResell licensing Manager", "description" => "This addon allows you to License the XtreamResell provisioning Module", "author" => "XtreamResell", "language" => "english", "version" => "1.0", "fields" => array("license" => array("FriendlyName" => "License key", "Type" => "text", "Size" => "40", "Default" => "", "Description" => "")));
}
/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 * Use this function to perform any database and schema modifications
 * required by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function AddonXtreamresell_activate()
{
    try {
        WHMCS\Database\Capsule::table("tbladdonmodules")->insert(array("module" => "addonxtreamresell", "setting" => "localkey"));
        return array("status" => "success", "description" => "XtreamResell licensing module has been successfully installed.");
    } catch (Exception $e) {
        return array("status" => "error", "description" => "Unable to create XtreamResell licensing module." . $e->getMessage());
    }
}
/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 * Use this function to undo any database and schema modifications
 * performed by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function AddonXtreamresell_deactivate()
{
    try {
        WHMCS\Database\Capsule::schema()->dropIfExists("mod_xtreamresell");
        WHMCS\Database\Capsule::table("tbladdonmodules")->where("module", "addonxtreamresell")->where("setting", "localkey")->delete();
        return array("status" => "success", "description" => "This is a demo module only. " . "In a real module you might report a success here.");
    } catch (Exception $e) {
        return array("status" => "error", "description" => "Unable to drop mod_xtreamresell: " . $e->getMessage());
    }
}
/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 * Use this function to perform any required database and schema modifications.
 *
 * This function is optional.
 *
 * @see https://laravel.com/docs/5.2/migrations
 *
 * @return void
 */
function AddonXtreamresell_upgrade($vars)
{
    $currentlyInstalledVersion = $vars["version"];
    if ($currentlyInstalledVersion < 1.1) {
        $schema = WHMCS\Database\Capsule::schema();
        $schema->table("mod_xtreamresell", function ($table) {
            $table->text("demo2");
        });
    }
    if ($currentlyInstalledVersion < 1.2) {
        $schema = WHMCS\Database\Capsule::schema();
        $schema->table("mod_xtreamresell", function ($table) {
            $table->text("demo3");
        });
    }
}
/**
 * Admin Area Output.
 *
 * Called when the addon module is accessed via the admin area.
 * Should return HTML output for display to the admin user.
 *
 * This function is optional.
 *
 * @see AddonXtreamresell\Admin\Controller::index()
 *
 * @return string
 */
function AddonXtreamresell_output($vars)
{
    $modulelink = $vars["modulelink"];
    $version = $vars["version"];
    $_lang = $vars["_lang"];
    $configTextField = $vars["license"];
    $action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
    $dispatcher = new WHMCS\Module\Addon\AddonXtreamresell\Admin\AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}
/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 * This function is optional.
 *
 * @param array $vars
 *
 * @return string
 */
function AddonXtreamresell_sidebar($vars)
{
    $modulelink = $vars["modulelink"];
    $version = $vars["version"];
    $_lang = $vars["_lang"];
    $configTextField = $vars["license"];
    $sidebar = "<p>Sidebar output HTML goes here</p>";
    return $sidebar;
}
/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 * Should return an array of output parameters.
 *
 * This function is optional.
 *
 * @see AddonXtreamresell\Client\Controller::index()
 *
 * @return array
 */
function AddonXtreamresell_clientarea($vars)
{
    $modulelink = $vars["modulelink"];
    $version = $vars["version"];
    $_lang = $vars["_lang"];
    $configTextField = $vars["license"];
    $action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
    $dispatcher = new WHMCS\Module\Addon\AddonXtreamresell\Client\ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}

?>