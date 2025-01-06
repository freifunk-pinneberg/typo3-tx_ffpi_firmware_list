<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FfpiFirmwareList',
    'Firmwarelist',
    [
        \FFPI\FfpiFirmwareList\Controller\FirmwareListController::class => 'list'
    ],
    // non-cacheable actions
    [
        \FFPI\FfpiFirmwareList\Controller\FirmwareListController::class => ''
    ]
);

// wizards
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ffpi_firmware_list/Configuration/TsConfig/Page/Mod/Wizards/NewContentElement.tsconfig">'
);
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'ffpi_firmware_list-plugin-firmwarelist',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:ffpi_firmware_list/ext_icon.svg']
);

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_firmware_list_cache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_firmware_list_cache'] = [];
}
