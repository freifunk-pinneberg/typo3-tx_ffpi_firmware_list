<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FFPI.FfpiFirmwareList',
    'Firmwarelist',
    [
        'FirmwareList' => 'list'
    ],
    // non-cacheable actions
    [
        'FirmwareList' => ''
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
