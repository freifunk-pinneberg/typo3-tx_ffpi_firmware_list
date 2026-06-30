<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FFPI\FfpiFirmwareList\Controller\FirmwareListController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

defined('TYPO3') || die('Access denied.');

ExtensionUtility::configurePlugin(
    'FfpiFirmwareList',
    'Firmwarelist',
    [
        FirmwareListController::class => 'list'
    ],
    // non-cacheable actions
    [
        FirmwareListController::class => ''
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
$iconRegistry->registerIcon(
    'ffpi_firmware_list-plugin-firmwarelist',
    SvgIconProvider::class,
    ['source' => 'EXT:ffpi_firmware_list/ext_icon.svg']
);
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_firmware_list_cache']) || !is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_firmware_list_cache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['ffpi_firmware_list_cache'] = [];
}
