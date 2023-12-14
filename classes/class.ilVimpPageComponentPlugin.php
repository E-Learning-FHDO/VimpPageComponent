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
    const TABLE_NAME = "copg_pgcp_vpco_config";
    const CTYPE = 'Services';
    const CNAME = 'COPage';
    const SLOT_ID = 'pgcp';
    const PLUGIN_ID = 'vpco';
    private static $instance;

    public function __construct()
    {
        global $DIC;
        $this->db = $DIC->database();
        parent::__construct($this->db, $DIC["component.repository"], self::PLUGIN_ID);
    }

    /**
     * Get plugin name
     *
     * @return string
     */
    function getPluginName(): string
    {
        return self::PLUGIN_NAME;
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

    public static function getInstance(): ilVimpPageComponentPlugin
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function setValue($setting, $value, $type)
    {
        global $DIC;
        $db = $DIC->database();

        $db->manipulate(
            "UPDATE " . ilVimpPageComponentPlugin::TABLE_NAME . " SET " .
            " value = " . $db->quote($value, $type) .
            " WHERE name = " . $db->quote($setting, "text")
        );
    }

    public static function getValue($setting)
    {
        global $DIC;
        $db = $DIC->database();
        $value = null;
        $set = $db->query(
            "SELECT value FROM " . ilVimpPageComponentPlugin::TABLE_NAME .
            " WHERE name = " . $db->quote($setting, "text")
        );

        if ($rec = $set->fetchRow()) {
            $value = $rec['value'];
        }
        return $value;
    }
} 

