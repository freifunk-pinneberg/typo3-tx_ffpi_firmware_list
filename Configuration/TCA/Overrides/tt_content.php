<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied.');

ExtensionUtility::registerPlugin(
    'FfpiFirmwareList',
    'Firmwarelist',
    'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_firmwarelist.title',
    null,
    'list'
);

ExtensionManagementUtility::addToAllTCAtypes('tt_content', '--div--;Configuration,pi_flexform,', 'ffpifirmwarelist_firmwarelist', 'after:subheader');
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    // Flexform configuration schema file
    'FILE:EXT:ffpi_firmware_list/Configuration/FlexForms/FirmwareList.xml',
    'ffpifirmwarelist_firmwarelist'
);
