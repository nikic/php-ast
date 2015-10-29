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

echo ast_dump(ast\parse_code($code, $version=10)), "\n";
echo ast_dump(ast\parse_code($code, $version=20)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN_OP
        flags: ASSIGN_BITWISE_OR (31)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    1: AST_ASSIGN_OP
        flags: ASSIGN_BITWISE_AND (32)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    2: AST_ASSIGN_OP
        flags: ASSIGN_BITWISE_XOR (33)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    3: AST_ASSIGN_OP
        flags: ASSIGN_CONCAT (30)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    4: AST_ASSIGN_OP
        flags: ASSIGN_ADD (23)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    5: AST_ASSIGN_OP
        flags: ASSIGN_SUB (24)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    6: AST_ASSIGN_OP
        flags: ASSIGN_MUL (25)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    7: AST_ASSIGN_OP
        flags: ASSIGN_DIV (26)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    8: AST_ASSIGN_OP
        flags: ASSIGN_MOD (27)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    9: AST_ASSIGN_OP
        flags: ASSIGN_POW (167)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    10: AST_ASSIGN_OP
        flags: ASSIGN_SHIFT_LEFT (28)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    11: AST_ASSIGN_OP
        flags: ASSIGN_SHIFT_RIGHT (29)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
AST_STMT_LIST
    0: AST_ASSIGN_OP
        flags: BINARY_BITWISE_OR (9)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    1: AST_ASSIGN_OP
        flags: BINARY_BITWISE_AND (10)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    2: AST_ASSIGN_OP
        flags: BINARY_BITWISE_XOR (11)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    3: AST_ASSIGN_OP
        flags: BINARY_CONCAT (8)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    4: AST_ASSIGN_OP
        flags: BINARY_ADD (1)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    5: AST_ASSIGN_OP
        flags: BINARY_SUB (2)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    6: AST_ASSIGN_OP
        flags: BINARY_MUL (3)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    7: AST_ASSIGN_OP
        flags: BINARY_DIV (4)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    8: AST_ASSIGN_OP
        flags: BINARY_MOD (5)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    9: AST_ASSIGN_OP
        flags: BINARY_POW (166)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    10: AST_ASSIGN_OP
        flags: BINARY_SHIFT_LEFT (6)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
    11: AST_ASSIGN_OP
        flags: BINARY_SHIFT_RIGHT (7)
        0: AST_VAR
            0: "a"
        1: AST_VAR
            0: "b"
