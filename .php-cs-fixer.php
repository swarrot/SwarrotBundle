<?php

$finder = new PhpCsFixer\Finder();
$finder
    ->in(__DIR__)
    ->exclude(__DIR__.'/vendor')
    ->name('*.php')
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;

return $config;
