<?php

declare(strict_types=1);

defined('TYPO3') or die();

// Show copied pages records in frontend request
$GLOBALS['TCA']['pages']['ctrl']['hideAtCopy'] = false;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_testirreforeignfieldnonws_hotels' => [
            'exclude' => true,
            'label' => 'LLL:EXT:test_irre_foreignfield_non_ws/Resources/Private/Language/locallang_db.xlf:pages.tx_testirreforeignfieldnonws_hotels',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_testirreforeignfieldnonws_hotel',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'maxitems' => 10,
                'appearance' => [
                    'showSynchronizationLink' => 1,
                    'showAllLocalizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                ],
            ],
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:test_irre_foreignfield_non_ws/Resources/Private/Language/locallang_db.xlf:pages.div.test_irre_foreignfield_non_ws, tx_testirreforeignfieldnonws_hotels'
);
