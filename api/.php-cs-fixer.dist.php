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
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_statement' => ['statements' => ['break','continue','return','throw','try']],
        'cast_spaces' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'date_time_immutable' => true,
        'declare_parentheses' => true,
        'final_class' => true,
        'final_public_method_for_abstract_class' => true,
        'fopen_flags' => ['b_mode' => true],
        'global_namespace_import' => true,
        'lambda_not_used_import' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_empty_phpdoc' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
        'no_unneeded_braces' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_useless_concat_operator' => true,
        'no_useless_else' => true,
        'no_useless_nullsafe_operator' => true,
        'no_useless_return' => true,
        'no_useless_sprintf' => true,
        'not_operator_with_successor_space' => true,
        'nullable_type_declaration' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => ['imports_order' => ['class','function','const']],
        'php_unit_attributes' => true,
        'php_unit_construct' => true,
        'php_unit_fqcn_annotation' => true,
        'php_unit_internal_class' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'phpdoc_array_type' => true,
        'phpdoc_list_type' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
        'self_static_accessor' => true,
        'static_lambda' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);
