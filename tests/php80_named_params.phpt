--TEST--
Named parameters in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$foo(first: 1, second: 2);
count(var: $argv);
$other->count(1, myVar:$foo, myVar: 1);  // error
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CALL
        expr: AST_VAR
            name: "foo"
        args: AST_ARG_LIST
            0: AST_NAMED_ARG
                name: "first"
                expr: 1
            1: AST_NAMED_ARG
                name: "second"
                expr: 2
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (%d)
            name: "count"
        args: AST_ARG_LIST
            0: AST_NAMED_ARG
                name: "var"
                expr: AST_VAR
                    name: "argv"
    2: AST_METHOD_CALL
        expr: AST_VAR
            name: "other"
        method: "count"
        args: AST_ARG_LIST
            0: 1
            1: AST_NAMED_ARG
                name: "myVar"
                expr: AST_VAR
                    name: "foo"
            2: AST_NAMED_ARG
                name: "myVar"
                expr: 1
