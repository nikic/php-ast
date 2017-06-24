--TEST--
Statement list normalization
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
while ($a);
while ($a) $b;
while ($a) { $b; }

declare(ticks=1);
declare(ticks=1) {}
PHP;

echo ast_dump(ast\parse_code($code, $version=35)), "\n";
echo ast_dump(ast\parse_code($code, $version=40)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: null
    1: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: AST_VAR
            name: "b"
    2: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: AST_STMT_LIST
            0: AST_VAR
                name: "b"
    3: AST_DECLARE
        declares: AST_CONST_DECL
            0: AST_CONST_ELEM
                name: "ticks"
                value: 1
        stmts: null
    4: AST_DECLARE
        declares: AST_CONST_DECL
            0: AST_CONST_ELEM
                name: "ticks"
                value: 1
        stmts: AST_STMT_LIST
AST_STMT_LIST
    0: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: AST_STMT_LIST
    1: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: AST_STMT_LIST
            0: AST_VAR
                name: "b"
    2: AST_WHILE
        cond: AST_VAR
            name: "a"
        stmts: AST_STMT_LIST
            0: AST_VAR
                name: "b"
    3: AST_DECLARE
        declares: AST_CONST_DECL
            0: AST_CONST_ELEM
                name: "ticks"
                value: 1
        stmts: null
    4: AST_DECLARE
        declares: AST_CONST_DECL
            0: AST_CONST_ELEM
                name: "ticks"
                value: 1
        stmts: AST_STMT_LIST
