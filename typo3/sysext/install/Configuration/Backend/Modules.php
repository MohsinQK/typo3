<?php

use TYPO3\CMS\Install\Controller\BackendModuleController;

/**
 * Definitions for modules provided by EXT:insatall
 */
return [
    'tools_toolsmaintenance' => [
        'parent' => 'tools',
        'access' => 'systemMaintainer',
        'path' => '/module/tools/maintenance',
        'iconIdentifier' => 'module-install-maintenance',
        'labels' => 'LLL:EXT:install/Resources/Private/Language/ModuleInstallMaintenance.xlf',
        'routes' => [
            '_default' => [
                'target' => BackendModuleController::class . '::maintenanceAction',
            ],
        ],
    ],
    'tools_toolssettings' => [
        'parent' => 'tools',
        'access' => 'systemMaintainer',
        'path' => '/module/tools/settings',
        'iconIdentifier' => 'module-install-settings',
        'labels' => 'LLL:EXT:install/Resources/Private/Language/ModuleInstallSettings.xlf',
        'routes' => [
            '_default' => [
                'target' => BackendModuleController::class . '::settingsAction',
            ],
        ],
    ],
    'tools_toolsupgrade' => [
        'parent' => 'tools',
        'access' => 'systemMaintainer',
        'path' => '/module/tools/upgrade',
        'iconIdentifier' => 'module-install-upgrade',
        'labels' => 'LLL:EXT:install/Resources/Private/Language/ModuleInstallUpgrade.xlf',
        'routes' => [
            '_default' => [
                'target' => BackendModuleController::class . '::upgradeAction',
            ],
        ],
    ],
    'tools_toolsenvironment' => [
        'parent' => 'tools',
        'access' => 'systemMaintainer',
        'path' => '/module/tools/environment',
        'iconIdentifier' => 'module-install-environment',
        'labels' => 'LLL:EXT:install/Resources/Private/Language/ModuleInstallEnvironment.xlf',
        'routes' => [
            '_default' => [
                'target' => BackendModuleController::class . '::environmentAction',
            ],
        ],
    ],
];
