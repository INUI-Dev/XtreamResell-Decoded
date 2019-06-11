<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace WHMCS\Module\Addon\AddonModule\Client;

/**
 * Sample Client Area Dispatch Handler
 */
class ClientDispatcher
{
    /**
     * Dispatch request.
     *
     * @param string $action
     * @param array $parameters
     *
     * @return array
     */
    public function dispatch($action, $parameters)
    {
        if (!$action) {
            $action = "index";
        }
        $controller = new Controller();
        if (is_callable(array($controller, $action))) {
            return $controller->{$action}($parameters);
        }
    }
}

?>