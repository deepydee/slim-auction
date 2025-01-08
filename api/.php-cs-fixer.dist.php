<?php

declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/public',
    ])
    ->append([
        __FILE__,
    ]);

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@DoctrineAnnotation' => true,
        '@PHP84Migration' => true,
        '@PHPUnit100Migration:risky' => true,

        'strict_param' => true,
        'strict_comparison' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'cast_spaces' => true,
        'concat_space' => ['spacing' => 'one'],

        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'no_empty_phpdoc' => true,
        'phpdoc_array_type' => true,
        'phpdoc_list_type' => true,
        'phpdoc_separation' => false,
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        'operator_linebreak' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'return', 'throw', 'try'],
        ],

        'php_unit_internal_class' => true,
        'php_unit_construct' => true,
        'php_unit_fqcn_annotation' => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],

        'final_class' => true,
        'final_public_method_for_abstract_class' => true,
        'self_static_accessor' => true,
        'static_lambda' => true,
        'global_namespace_import' => true,
        'not_operator_with_successor_space' => true,

        'fopen_flags' => ['b_mode' => true],
    ])
    ->setFinder($finder);
