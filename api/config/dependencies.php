<?php

declare(strict_types=1);

$files = glob(__DIR__ . '/common/*.php');

$configs = array_map(static fn ($file) => require $file, $files);

return array_merge_recursive(...$configs);
