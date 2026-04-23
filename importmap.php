<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'apexcharts' => [
        'version' => '4.4.0',
    ],
    'tom-select/dist/css/tom-select.default.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'tom-select' => [
        'version' => '2.4.3',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
    'tom-select/dist/css/tom-select.default.min.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
    'tom-select/dist/css/tom-select.bootstrap4.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'tom-select/dist/css/tom-select.bootstrap5.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    '@sylius/bootstrap-admin-ui/entrypoint' => [
        'path' => './src/BootstrapAdminUi/assets/entrypoint.js',
        'entrypoint' => true,
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '5.15.4',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '5.15.4',
        'type' => 'css',
    ],
    '@tabler/core' => [
        'version' => '1.4.0',
    ],
    '@tabler/core/dist/css/tabler.min.css' => [
        'version' => '1.4.0',
        'type' => 'css',
    ],
    'basiclightbox' => [
        'version' => '5.0.4',
    ],
    'choices.js' => [
        'version' => '10.2.0',
    ],
    'codemirror' => [
        'version' => '5.65.21',
    ],
    'codemirror/lib/codemirror.min.css' => [
        'version' => '5.65.21',
        'type' => 'css',
    ],
    'dirty-form' => [
        'version' => '0.4.0',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'mark.js' => [
        'version' => '8.11.1',
    ],
    'slugify' => [
        'version' => '1.6.8',
    ],
];
