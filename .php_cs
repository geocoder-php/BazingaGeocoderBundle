<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exlude(__DIR__.'/vendor')
;
return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;
