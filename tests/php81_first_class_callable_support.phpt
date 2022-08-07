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
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            flags: 0
            name: "c1"
        expr: AST_CALL
            expr: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "foo"
            args: AST_CALLABLE_CONVERT
    1: AST_ASSIGN
        var: AST_VAR
            flags: 0
            name: "c2"
        expr: AST_STATIC_CALL
            class: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "C"
            method: "foo"
            args: AST_CALLABLE_CONVERT
    2: AST_ASSIGN
        var: AST_VAR
            flags: 0
            name: "c2"
        expr: AST_METHOD_CALL
            expr: AST_VAR
                flags: 0
                name: "x"
            method: "foo"
            args: AST_CALLABLE_CONVERT