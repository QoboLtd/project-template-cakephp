<?php

use App\View\AppView;
use Cake\Core\Configure;

// CsvMigrations plugin configuration
return [
    'CsvMigrations' => [
        'appView' => AppView::class,
        'tableValidation' => false,
        // Configuration options for the ValidateShell
        'ValidateShell' => [
            // Module-specific configuration options
            'module' => [
                // Default module options (used if no module-specific options given)
                '_default' => [
                    // The list of checks to perform during the module validation.
                    // Checks are an associative array of classes (keys) and options
                    // (values).  Classes have exist and implement the
                    // CsvMigrations\Utility\Validate\Check\CheckInterface or an
                    // exception will be thrown during the validation run.
                    'checks' => [
                        'CsvMigrations\\Utility\\Validate\\Check\\ConfigCheck' => [
                            // List of fields, which are not allowed to be used as
                            // display_field.  For example: id.
                            'display_field_bad_values' => ['id'],
                            // List of icons, which are not allowed to be used as
                            // module icons.  For example: cube.
                            'icon_bad_values' => ['cube'],
                        ],
                    ],
                ],
                // Common module is not used for any display purposes, so it's fine for
                // it not to have any strict validations
                'Common' => [
                    'checks' => [
                        'CsvMigrations\\Utility\\Validate\\Check\\ConfigCheck' => [
                            'display_field_bad_values' => [],
                            'icon_bad_values' => [],
                        ],
                    ],
                ],
            ],
        ],
        // Helper configuration : see App/View/Helper/MyHtmlHelper::class
        'HelperConfig' => [
            'dataToggle' => 'tooltip',
            'dataPlacement' => 'auto bottom',
        ],
    ],
];
