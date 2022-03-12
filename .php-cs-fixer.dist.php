<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        '@PHP80Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
        'binary_operator_spaces' => [
            'operators' => [
                '|' => 'no_space',
            ],
        ],
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'concat_space' => ['spacing' => 'none'],
        'class_attributes_separation' => ['elements' =>  ['method' => 'one']],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'yoda_style' => true,
        'list_syntax' => ['syntax' => 'short'],
        'void_return' => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache') // forward compatibility with 3.x line
;
