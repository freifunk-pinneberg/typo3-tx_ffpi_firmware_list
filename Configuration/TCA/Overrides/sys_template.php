<?php

declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'ffpi_firmware_list',
    'Configuration/TypoScript',
    'Firmware Liste'
);
