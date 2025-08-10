--TEST--
Complex exit
--SKIPIF--
<?php if (PHP_VERSION_ID < 80500) die('skip PHP >= 8.5 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
exit($foo, $bar);
exit(a: $foo);
exit(...$foo);
exit(...);
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=120);
echo ast_dump($node), "\n";
--EXPECT--
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_VAR
                name: "foo"
            1: AST_VAR
                name: "bar"
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_NAMED_ARG
                name: "a"
                expr: AST_VAR
                    name: "foo"
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_UNPACK
                expr: AST_VAR
                    name: "foo"
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_CALLABLE_CONVERT
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_VAR
                name: "foo"
            1: AST_VAR
                name: "bar"
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_NAMED_ARG
                name: "a"
                expr: AST_VAR
                    name: "foo"
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_UNPACK
                expr: AST_VAR
                    name: "foo"
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_CALLABLE_CONVERT
