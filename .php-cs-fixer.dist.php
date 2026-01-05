<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(__DIR__.'/vendor')
    ->notPath('phpstan-baseline.php')
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['var']], // phpstan errors pops up without this
    ])
;
