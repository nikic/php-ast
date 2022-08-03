<?php

/** @generate-function-entries */

/**
 * ========================================================================================
 * | USE ast_stub.php INSTEAD IF YOU ARE LOOKING FOR DOCUMENTATION OR STUBS FOR YOUR IDE. |
 * ========================================================================================
 *
 * This is a stub file meant only for use with https://github.com/php/php-src/blob/master/build/gen_stub.php
 * to generate Reflection information (ReflectionParameter, ReflectionFunction, ReflectionMethod, etc.)
 */

namespace ast;

// XXX: @param in doc comments will cause build/gen_stub.php to emit an error if there is already a real type in the latest php versions.
// Use ast_stub.php instead for documentation.

function parse_code(string $code, int $version, string $filename = 'string code'): \ast\Node {}

function parse_file(string $filename, int $version): \ast\Node {}

function get_kind_name(int $kind): string {}

function kind_uses_flags(int $kind): bool {}

function get_metadata(): array {}

function get_supported_versions(bool $exclude_deprecated = false): array {}

// In php 8.2+, ast\Node implements the attribute AllowDynamicProperties
/**
 * This class describes a single node in a PHP AST.
 */
#[\AllowDynamicProperties]
class Node
{
    public function __construct(?int $kind = null, ?int $flags = null, ?array $children = null, ?int $lineno = null) {
    }

    public static function parseCode(string $code, int $version, string $filename = 'string code'): static {
    }

    public static function parseFile(string $filename, int $version): static {
    }
}

