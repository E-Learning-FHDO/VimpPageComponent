<#1>
<?php

/**
 * @var $ilDB ilDB
 */

$fields = [
    'name' => [
        'type' => 'text',
        'length' => 100,
        'notnull' => true,
    ],
    'value' => [
        'type' => 'text',
        'notnull' => false,
        'default' => null
    ]
 ];

if (!$ilDB->tableExists('copg_pgcp_vpco_config')) {
    $ilDB->createTable('copg_pgcp_vpco_config', $fields);
    $ilDB->addPrimaryKey('copg_pgcp_vpco_config', ['name']);
}

$ilDB->insert('copg_pgcp_vpco_config', [
    'setting' => ['text', 'default_width'],
    'value' => null
]);

$ilDB->insert('copg_pgcp_vpco_config', [
    'setting' => ['text', 'default_height'],
    'value' => null
]);

?>
