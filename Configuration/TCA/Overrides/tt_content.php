<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'FFPI.FfpiFirmwareList',
    'Firmwarelist',
    'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_firmwarelist.title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['ffpifirmwarelist_firmwarelist'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'ffpifirmwarelist_firmwarelist',
    // Flexform configuration schema file
    'FILE:EXT:ffpi_firmware_list/Configuration/FlexForms/FirmwareList.xml'
);