<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(__DIR__.'/vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
