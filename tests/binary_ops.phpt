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

echo ast_dump(ast\parse_code($code, $version=10)), "\n";
echo ast_dump(ast\parse_code($code, $version=20)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_GREATER
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    1: AST_GREATER_EQUAL
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    2: AST_AND
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    3: AST_OR
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
AST_STMT_LIST
    0: AST_BINARY_OP
        flags: BINARY_IS_GREATER (256)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"
    1: AST_BINARY_OP
        flags: BINARY_IS_GREATER_OR_EQUAL (257)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"
    2: AST_BINARY_OP
        flags: BINARY_BOOL_AND (259)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"
    3: AST_BINARY_OP
        flags: BINARY_BOOL_OR (258)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"
