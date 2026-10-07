<?php

$autoloadFiles = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($autoloadFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
        return;
    }
}

throw new RuntimeException('Unable to find autoload.php for testing');
