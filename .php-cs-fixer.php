<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(__DIR__.'/vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['var']], // phpstan errors pops up without this
        'unsupportedPhpVersionAllowed' => true,
    ])
    ->setFinder($finder)
;
