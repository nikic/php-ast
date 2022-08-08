--TEST--
Intersection types in php 8.1
--SKIPIF--
<?php if (PHP_VERSION_ID < 80100) die('skip PHP >= 8.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class X {
    public Countable&ArrayAccess&Traversable $arrayLike;
    public function example(Throwable&Countable $tc): self&Countable {
        throw $tc;
    }
}
// Using iterable, int, etc are compilation errors, not parse errors - programs using the output of php-ast will have to check for unsupported primitive types
// (Fatal error: Type int cannot be part of an intersection type)
function this_is_a_compile_error(): iterable&Countable {}
PHP;

$node = ast\parse_code($code, $version=80);
echo ast_dump($node), "\n";

--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: AST_TYPE_INTERSECTION
                    0: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "Countable"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "ArrayAccess"
                    2: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "Traversable"
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "arrayLike"
                        default: null
                        docComment: null
                attributes: null
            1: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "example"
                docComment: null
                params: AST_PARAM_LIST
                    0: AST_PARAM
                        type: AST_TYPE_INTERSECTION
                            0: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Throwable"
                            1: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Countable"
                        name: "tc"
                        default: null
                        attributes: null
                        docComment: null
                stmts: AST_STMT_LIST
                    0: AST_THROW
                        expr: AST_VAR
                            name: "tc"
                returnType: AST_TYPE_INTERSECTION
                    0: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "self"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "Countable"
                attributes: null
                __declId: 0
        attributes: null
        __declId: 1
    1: AST_FUNC_DECL
        name: "this_is_a_compile_error"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: AST_TYPE_INTERSECTION
            0: AST_TYPE
                flags: TYPE_ITERABLE (%d)
            1: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "Countable"
        attributes: null
        __declId: 2