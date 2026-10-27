<?php


return [


    'directus' => [
        'endpoint' => env('DIRECTUS_URL', ''),
        'token' => env('DIRECTUS_ACCESS_TOKEN', ''),
    ],

    'elastic' => [
        'scheme' => env('ELASTIC_SCHEME'),
        'host' => env('ELASTIC_API'),
        'path' => env('ELASTIC_PATH'),
    ],

    'adlib' => [
        'uri' => env('ADLIBURI'),
    ],

    'recaptcha' => [
        'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
    ],

    'main_url' => env('MAIN_URL'),
    'ulan' => env('FITZ_ULAN'),
    'ciim_image_url' => env('CIIM_IMAGE_URL'),
    'image_url' => env('FITZ_IMAGE_URL'),
    'iip_url' => env('FITZ_IIP_URL'),
    'manifest_url' => env('FITZ_MANIFEST_URL'),
    'uv_viewer_path' => env('FITZ_UV_VIEWER_PATH'),


];
