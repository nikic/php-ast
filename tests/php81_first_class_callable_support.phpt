--TEST--
First-class callables in php 8.1
--SKIPIF--
<?php if (PHP_VERSION_ID < 80100) die('skip PHP >= 8.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$c1 = foo(...);
$c2 = C::foo(...);
$c2 = $x->foo(...);
PHP;

$node = ast\parse_code($code, $version=80);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "c1"
        expr: AST_CALL
            expr: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "foo"
            args: AST_CALLABLE_CONVERT
    1: AST_ASSIGN
        var: AST_VAR
            name: "c2"
        expr: AST_STATIC_CALL
            class: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "C"
            method: "foo"
            args: AST_CALLABLE_CONVERT
    2: AST_ASSIGN
        var: AST_VAR
            name: "c2"
        expr: AST_METHOD_CALL
            expr: AST_VAR
                name: "x"
            method: "foo"
            args: AST_CALLABLE_CONVERT