<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'vendor', 'node_modules'])

;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        // @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/rules/import/ordered_imports.rst
        'ordered_imports' => ['imports_order' => ['const', 'class', 'function']],
    ])

    ->setFinder($finder)
;
