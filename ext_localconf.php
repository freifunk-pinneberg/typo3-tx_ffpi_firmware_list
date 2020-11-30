<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'FFPI.FfpiFirmwareList',
            'FirmwareList',
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
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        firmware_list {
                            iconIdentifier = ffpi_firmware_list-plugin-firmware-list
                            title = LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang_db.xlf:tx_ffpi_firmware_list_firmware_list.name
                            description = LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang_db.xlf:tx_ffpi_firmware_list_firmware_list.description
                            tt_content_defValues {
                                CType = list
                                list_type = ffpifirmwarelist_firmware_list
                            }
                        }
                    }
                    show = *
                }
           }'
        );
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'ffpi_firmware_list-plugin-firmware-list',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:ffpi_firmware_list/ext_icon.svg']
        );
    }
);
