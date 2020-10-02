<?php

/** @generate-function-entries */

/**
 * USE ast_stub.php INSTEAD IF YOU ARE LOOKING FOR DOCUMENTATION OR STUBS FOR YOUR IDE.
 *
 * This is a stub file meant only for use with https://github.com/php/php-src/blob/master/build/gen_stub.php
 * to generate Reflection information (ReflectionParameter, ReflectionFunction, ReflectionMethod, etc.)
 */

namespace ast;

/**
 * Parses code file and returns AST root node.
 *
 * @param string $filename Code file to parse
 * @param int    $version  AST version
 * @return Node Root node of AST
 *
 * @see https://github.com/nikic/php-ast for version information
 */
function parse_code(string $code, int $version, string $filename = 'string code'): \ast\Node {}

function parse_file(string $filename, int $version): \ast\Node {}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return string String representation of AST kind value
 */
function get_kind_name(int $kind): string {}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return bool Returns true if AST kind uses flags
 */
function kind_uses_flags(int $kind): bool {}

/**
 * Provides metadata for the AST kinds.
 *
 * The returned array is a map from AST kind to a Metadata object.
 *
 * @return Metadata[] Metadata about AST kinds
 */
function get_metadata(): array {}

/**
 * Returns currently supported AST versions.
 *
 * @param bool $exclude_deprecated Whether to exclude deprecated versions
 * @return int[] Array of supported AST versions
 */
function get_supported_versions(bool $exclude_deprecated = false): array {}

/**
 * This class describes a single node in a PHP AST.
 */
class Node
{
    /**
     * A constructor which accepts any types for the properties.
     * For backwards compatibility reasons, all values are optional and can be any type, and properties default to null
     *
     * @param int|null $kind
     * @param int|null $flags
     * @param array|null $children
     * @param int|null $lineno
     */
    public function __construct(?int $kind = null, ?int $flags = null, ?array $children = null, ?int $lineno = null) {
    }
}

