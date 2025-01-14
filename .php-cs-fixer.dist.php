<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_align' => false,
        // Don't remove @param that define collection and array generics (yes they should have complete comments, but).
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setFinder($finder)
;
