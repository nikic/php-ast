--TEST--
AST_GREATER(_EQUAL) converted to AST_BINARY_OP
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a > $b;
$a >= $b;
$a and $b;
$a or $b;
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_BINARY_OP
        flags: BINARY_IS_GREATER (256)
        left: AST_VAR
            flags: 0
            name: "a"
        right: AST_VAR
            flags: 0
            name: "b"
    1: AST_BINARY_OP
        flags: BINARY_IS_GREATER_OR_EQUAL (257)
        left: AST_VAR
            flags: 0
            name: "a"
        right: AST_VAR
            flags: 0
            name: "b"
    2: AST_BINARY_OP
        flags: BINARY_BOOL_AND (259)
        left: AST_VAR
            flags: 0
            name: "a"
        right: AST_VAR
            flags: 0
            name: "b"
    3: AST_BINARY_OP
        flags: BINARY_BOOL_OR (258)
        left: AST_VAR
            flags: 0
            name: "a"
        right: AST_VAR
            flags: 0
            name: "b"
