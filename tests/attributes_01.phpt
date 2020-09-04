--TEST--
Attributes in PHP 8.0 on globals
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace NS;

#[SomeAttribute]
function test(#[namespace\SomeAttribute(2+2)] Type $arg) {
}

$x = #[SomeAttribute] function () {};

$y = #[SomeAttribute] fn (#[\SomeAttribute] $a) => $x;
PHP;

echo ast_dump(ast\parse_code($code, $version=70));
--EXPECTF--
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "NS"
        stmts: null
    1: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_NOT_FQ (%d)
                    name: "Type"
                name: "arg"
                default: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    2: AST_ASSIGN
        var: AST_VAR
            name: "x"
        expr: AST_CLOSURE
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
            uses: null
            stmts: AST_STMT_LIST
            returnType: null
            __declId: 1
    3: AST_ASSIGN
        var: AST_VAR
            name: "y"
        expr: AST_ARROW_FUNC
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
            stmts: AST_RETURN
                expr: AST_VAR
                    name: "x"
            returnType: null
            __declId: 2