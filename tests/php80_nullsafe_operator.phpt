--TEST--
Nullsafe operator in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$foo?->bar(2);
$a = $b?->c;
$a = new $b?->c;
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_NULLSAFE_METHOD_CALL
        expr: AST_VAR
            name: "foo"
        method: "bar"
        args: AST_ARG_LIST
            0: 2
    1: AST_ASSIGN
        var: AST_VAR
            name: "a"
        expr: AST_NULLSAFE_PROP
            expr: AST_VAR
                name: "b"
            prop: "c"
    2: AST_ASSIGN
        var: AST_VAR
            name: "a"
        expr: AST_NEW
            class: AST_NULLSAFE_PROP
                expr: AST_VAR
                    name: "b"
                prop: "c"
            args: AST_ARG_LIST