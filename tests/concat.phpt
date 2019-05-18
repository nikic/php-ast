--TEST--
AST_CONCAT with/without parenthesis
--FILE--
<?php
// PHP 7.4 changed the internal representation of concatenation operations.
// This tests that php-ast consistently exposes the AST in PHP 7.0-7.4
// For https://github.com/nikic/php-ast/issues/123
require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
require_once __DIR__ . '/first.php';
require_once(__DIR__ . '/second.php');
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_INCLUDE_OR_EVAL
        flags: EXEC_REQUIRE_ONCE (%d)
        expr: AST_BINARY_OP
            flags: BINARY_CONCAT (%d)
            left: AST_MAGIC_CONST
                flags: MAGIC_DIR (%d)
            right: "/first.php"
    1: AST_INCLUDE_OR_EVAL
        flags: EXEC_REQUIRE_ONCE (%d)
        expr: AST_BINARY_OP
            flags: BINARY_CONCAT (%d)
            left: AST_MAGIC_CONST
                flags: MAGIC_DIR (%d)
            right: "/second.php"
