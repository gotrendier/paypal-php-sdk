<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(dirname(__DIR__) . '/lib')
);

$classes = [];
foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
        continue;
    }

    $code = file_get_contents($fileInfo->getPathname());
    if (!preg_match('/^(?:abstract\s+)?class\s+(\w+)/m', $code)) {
        continue;
    }

    $relativePath = substr($fileInfo->getPathname(), strlen(dirname(__DIR__) . '/lib/'));
    $classes[] = 'PayPal\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
}

foreach ($classes as $class) {
    class_exists($class);
}

$context = new PayPal\Rest\ApiContext(new PayPal\Auth\OAuthTokenCredential('id', 'secret'));
$payment = new PayPal\Api\Payment();
$payment->toJSON();
