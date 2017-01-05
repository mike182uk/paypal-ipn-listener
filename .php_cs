<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/features')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
          'syntax' => 'short',
        ],
    ])
    ->setUsingCache(false)
    ->setFinder($finder)
;
