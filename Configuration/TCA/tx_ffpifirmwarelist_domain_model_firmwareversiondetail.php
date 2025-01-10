<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail',
        'label' => 'version',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => false,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'node_id,node_name,role,online,last_change,',
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/svgs/actions/actions-git.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,version,gluon_release,openwrt_release,has_security_issues,additional_notes,git,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime,endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_ffpifirmwarelist_domain_model_firmwareversiondetail',
                'foreign_table_where' => 'AND tx_ffpifirmwarelist_domain_model_firmwareversiondetail.pid=###CURRENT_PID### AND tx_ffpifirmwarelist_domain_model_firmwareversiondetail.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => strtotime('today midnight')
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => strtotime('today midnight')
                ],
            ],
        ],
        'version' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.version',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'gluon_release' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.gluon_release',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'l10n_mode' => 'exclude'
            ],
        ],
        'openwrt_release' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.openwrt_release',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'l10n_mode' => 'exclude'
            ],
        ],
        'has_security_issues' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.has_security_issues',
            'config' => [
                'type' => 'check',
                'l10n_mode' => 'exclude'
            ],
        ],
        'additional_notes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.additional_notes',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 8,
            ],
        ],
        'git' => [
            'label' => 'LLL:EXT:ffpi_firmware_list/Resources/Private/Language/locallang.xlf:tx_ffpifirmwarelist_domain_model_firmwareversiondetail.git',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'l10n_mode' => 'exclude'
            ],
        ],
    ],
];
