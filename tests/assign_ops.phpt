--TEST--
Assign op flags
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip ast extension not loaded";
if (!extension_loaded("tokenizer")) print "skip tokenizer extension not loaded";
?>
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

echo ast_dump(ast\parse_code($code, $version=40)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN_OP
        flags: BINARY_BITWISE_OR (9)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    1: AST_ASSIGN_OP
        flags: BINARY_BITWISE_AND (10)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    2: AST_ASSIGN_OP
        flags: BINARY_BITWISE_XOR (11)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    3: AST_ASSIGN_OP
        flags: BINARY_CONCAT (8)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    4: AST_ASSIGN_OP
        flags: BINARY_ADD (1)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    5: AST_ASSIGN_OP
        flags: BINARY_SUB (2)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    6: AST_ASSIGN_OP
        flags: BINARY_MUL (3)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    7: AST_ASSIGN_OP
        flags: BINARY_DIV (4)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    8: AST_ASSIGN_OP
        flags: BINARY_MOD (5)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    9: AST_ASSIGN_OP
        flags: BINARY_POW (166)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    10: AST_ASSIGN_OP
        flags: BINARY_SHIFT_LEFT (6)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
    11: AST_ASSIGN_OP
        flags: BINARY_SHIFT_RIGHT (7)
        var: AST_VAR
            name: "a"
        expr: AST_VAR
            name: "b"
