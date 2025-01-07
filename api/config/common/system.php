<?php

declare(strict_types=1);

$env = getenv('APP_ENV');
if ($env === false) {
    $env = 'prod';
}

return [
    'config' => [
        'env' => $env,
        'debug' => (bool) getenv('APP_DEBUG'),
    ],
];
