<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Configuration Values
    |--------------------------------------------------------------------------
    |
    | Extra config on top of config/backpack/ui.php. Values here override
    | config/backpack/ui.php when this theme is in use.
    |
    */

    'layout' => 'vertical',

    'auth_layout' => 'default',

    'styles' => [
        base_path('vendor/backpack/theme-tabler/resources/assets/css/skins/backpack-color-palette.css'),
        base_path('vendor/backpack/theme-tabler/resources/assets/css/skins/glass.css'),
        base_path('vendor/backpack/theme-tabler/resources/assets/css/skins/fuzzy-background.css'),
    ],

    'options' => [
        'colorModes' => [
            'system' => 'la-desktop',
            'light' => 'la-sun',
            'dark' => 'la-moon',
        ],
        'defaultColorMode' => 'system',
        'showColorModeSwitcher' => true,
        'useStickyHeader' => false,
        'useFluidContainers' => true,
        'sidebarFixed' => false,
        'doubleTopBarInHorizontalLayouts' => false,
        'showPasswordVisibilityToggler' => false,
    ],

    'classes' => [
        'body' => null,
        'topHeader' => null,
        'sidebar' => null,
        'menuHorizontalContainer' => null,
        'menuHorizontalContent' => null,
        'footer' => null,
        'table' => null,
        'tableWrapper' => 'crud-table-wrapper',
    ],
];
