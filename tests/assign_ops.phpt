--TEST--
Assign op flags
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a |= $b;
$a &= $b;
$a ^= $b;
$a .= $b;
$a += $b;
$a -= $b;
$a *= $b;
$a /= $b;
$a %= $b;
$a **= $b;
$a <<= $b;
$a >>= $b;
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN_OP
        flags: BINARY_BITWISE_OR (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    1: AST_ASSIGN_OP
        flags: BINARY_BITWISE_AND (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    2: AST_ASSIGN_OP
        flags: BINARY_BITWISE_XOR (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    3: AST_ASSIGN_OP
        flags: BINARY_CONCAT (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    4: AST_ASSIGN_OP
        flags: BINARY_ADD (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    5: AST_ASSIGN_OP
        flags: BINARY_SUB (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    6: AST_ASSIGN_OP
        flags: BINARY_MUL (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    7: AST_ASSIGN_OP
        flags: BINARY_DIV (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    8: AST_ASSIGN_OP
        flags: BINARY_MOD (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    9: AST_ASSIGN_OP
        flags: BINARY_POW (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    10: AST_ASSIGN_OP
        flags: BINARY_SHIFT_LEFT (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"
    11: AST_ASSIGN_OP
        flags: BINARY_SHIFT_RIGHT (%d)
        var: AST_VAR
            flags: 0
            name: "a"
        expr: AST_VAR
            flags: 0
            name: "b"