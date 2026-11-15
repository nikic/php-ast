--TEST--
Partial function application in PHP 8.6
--SKIPIF--
<?php if (PHP_VERSION_ID < 80600) die('skip PHP >= 8.6 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
f(1, ?, $x);
f(1, ...);
f(?, c: ?, ...);
f(...);
$o->m(?, b: 2);
$o?->m(?);
C::s(?, ...);
$x |> f(1, ?);
PHP;

$node = ast\parse_code($code, $version=120);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=130);
echo ast_dump($node), "\n";
--EXPECT--
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
    4: AST_METHOD_CALL
        expr: AST_VAR
            name: "o"
        method: "m"
        args: AST_CALLABLE_CONVERT
    5: AST_NULLSAFE_METHOD_CALL
        expr: AST_VAR
            name: "o"
        method: "m"
        args: AST_CALLABLE_CONVERT
    6: AST_STATIC_CALL
        class: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "C"
        method: "s"
        args: AST_CALLABLE_CONVERT
    7: AST_BINARY_OP
        flags: BINARY_PIPE (261)
        left: AST_VAR
            name: "x"
        right: AST_CALL
            expr: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "f"
            args: AST_CALLABLE_CONVERT
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: 1
                1: AST_PLACEHOLDER_ARG
                2: AST_VAR
                    name: "x"
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: 1
                1: AST_PLACEHOLDER_ARG
                    flags: PLACEHOLDER_VARIADIC (1)
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: AST_PLACEHOLDER_ARG
                1: AST_NAMED_ARG
                    name: "c"
                    expr: AST_PLACEHOLDER_ARG
                2: AST_PLACEHOLDER_ARG
                    flags: PLACEHOLDER_VARIADIC (1)
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "f"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: AST_PLACEHOLDER_ARG
                    flags: PLACEHOLDER_VARIADIC (1)
    4: AST_METHOD_CALL
        expr: AST_VAR
            name: "o"
        method: "m"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: AST_PLACEHOLDER_ARG
                1: AST_NAMED_ARG
                    name: "b"
                    expr: 2
    5: AST_NULLSAFE_METHOD_CALL
        expr: AST_VAR
            name: "o"
        method: "m"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: AST_PLACEHOLDER_ARG
    6: AST_STATIC_CALL
        class: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "C"
        method: "s"
        args: AST_CALLABLE_CONVERT
            args: AST_ARG_LIST
                0: AST_PLACEHOLDER_ARG
                1: AST_PLACEHOLDER_ARG
                    flags: PLACEHOLDER_VARIADIC (1)
    7: AST_BINARY_OP
        flags: BINARY_PIPE (261)
        left: AST_VAR
            name: "x"
        right: AST_CALL
            expr: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "f"
            args: AST_CALLABLE_CONVERT
                args: AST_ARG_LIST
                    0: 1
                    1: AST_PLACEHOLDER_ARG
