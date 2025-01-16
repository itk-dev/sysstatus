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
    'jquery' => [
        'version' => '3.7.1',
    ],
    'select2' => [
        'version' => '4.1.0-rc.0',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.1.0-rc.0',
        'type' => 'css',
    ],
    'region-align' => [
        'version' => '2.1.3',
    ],
    'object-assign' => [
        'version' => '3.0.0',
    ],
    'escape-html' => [
        'version' => '1.0.3',
    ],
    'to-style' => [
        'version' => '1.3.3',
    ],
    'react-style-normalizer' => [
        'version' => '1.2.8',
    ],
    'clone' => [
        'version' => '1.0.4',
    ],
    'matches-selector' => [
        'version' => '1.2.0',
    ],
    'contains' => [
        'version' => '0.1.1',
    ],
    'region' => [
        'version' => '2.1.2',
    ],
    'hasown' => [
        'version' => '1.0.1',
    ],
    'newify' => [
        'version' => '1.1.9',
    ],
    'jquery-once' => [
        'version' => '2.3.0',
    ],
];
