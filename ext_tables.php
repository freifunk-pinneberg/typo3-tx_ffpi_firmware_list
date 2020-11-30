<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'FFPI.FfpiFirmwareList',
            'FirmwareList',
            'Firmware Liste'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ffpi_firmware_list', 'Configuration/TypoScript', 'Firmware Liste');
    }
);
