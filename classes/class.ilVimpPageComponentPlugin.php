<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class ilVimpPageComponentPlugin
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilVimpPageComponentPlugin extends ilPageComponentPlugin {

    const PLUGIN_NAME = 'VimpPageComponent';

    /**
     * Get plugin name
     *
     * @return string
     */
    function getPluginName()
    {
        return "VimpPageComponent";
    }


    /**
     * Get plugin name
     *
     * @return string
     */
    function isValidParentType($a_parent_type)
    {
        return true;
    }

} 

