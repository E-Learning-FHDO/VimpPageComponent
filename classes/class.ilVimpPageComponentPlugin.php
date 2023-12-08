<?php

declare(strict_types=1);

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
    function getPluginName(): string
    {
        return "VimpPageComponent";
    }


    /**
     * Get plugin name
     *
     * @return string
     */
    function isValidParentType($a_parent_type): bool
    {
        return true;
    }

} 

