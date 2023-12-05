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
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true, # PHPStan will complain about missing types otherwise
        ],
    ])
    ->setFinder($finder)
;

return $config;
