--TEST--
Match expression in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$x = match($y) { 2 => 3, default => 5 };

match(1) {};
match(my_const) {
	1, \other_const => $x,
};
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "x"
        expr: AST_MATCH
            cond: AST_VAR
                name: "y"
            stmts: AST_MATCH_ARM_LIST
                0: AST_MATCH_ARM
                    cond: AST_EXPR_LIST
                        0: 2
                    expr: 3
                1: AST_MATCH_ARM
                    cond: null
                    expr: 5
    1: AST_MATCH
        cond: 1
        stmts: AST_MATCH_ARM_LIST
    2: AST_MATCH
        cond: AST_CONST
            name: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "my_const"
        stmts: AST_MATCH_ARM_LIST
            0: AST_MATCH_ARM
                cond: AST_EXPR_LIST
                    0: 1
                    1: AST_CONST
                        name: AST_NAME
                            flags: NAME_FQ (%d)
                            name: "other_const"
                expr: AST_VAR
                    name: "x"