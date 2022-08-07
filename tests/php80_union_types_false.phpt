--TEST--
Union types in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Example {
    public iterable|null|false $value;
}
// These are semantic errors, not syntax errors
$f = fn(OBJECT|false $a) : false => $a;
$g = function (false $arg) : false|null { return false; };
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "Example"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: AST_TYPE_UNION
                    0: AST_TYPE
                        flags: TYPE_ITERABLE (%d)
                    1: AST_TYPE
                        flags: TYPE_NULL (%d)
                    2: AST_TYPE
                        flags: TYPE_FALSE (%d)
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "value"
                        default: null
                        docComment: null
        __declId: 0
    1: AST_ASSIGN
        var: AST_VAR
            flags: 0
            name: "f"
        expr: AST_ARROW_FUNC
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: AST_TYPE_UNION
                        0: AST_TYPE
                            flags: TYPE_OBJECT (%d)
                        1: AST_TYPE
                            flags: TYPE_FALSE (%d)
                    name: "a"
                    default: null
            stmts: AST_RETURN
                expr: AST_VAR
                    flags: 0
                    name: "a"
            returnType: AST_TYPE
                flags: TYPE_FALSE (%d)
            __declId: 1
    2: AST_ASSIGN
        var: AST_VAR
            flags: 0
            name: "g"
        expr: AST_CLOSURE
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: AST_TYPE
                        flags: TYPE_FALSE (%d)
                    name: "arg"
                    default: null
            uses: null
            stmts: AST_STMT_LIST
                0: AST_RETURN
                    expr: AST_CONST
                        name: AST_NAME
                            flags: NAME_NOT_FQ (%d)
                            name: "false"
            returnType: AST_TYPE_UNION
                0: AST_TYPE
                    flags: TYPE_FALSE (%d)
                1: AST_TYPE
                    flags: TYPE_NULL (%d)
            __declId: 2
