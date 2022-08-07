--TEST--
Union types in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace NS;
function test(OBJECT|array|float|int|String|null $a) : string|false {
    return json_encode($a);
}
class Xyz {
    public bool|stdClass $x;
}
function testClasses(iterable|\stdClass|Xyz $s) : namespace\Xyz|false|null {
    return new X();
}
test([]);
testClasses([2,3]);
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
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
                type: AST_TYPE_UNION
                    0: AST_TYPE
                        flags: TYPE_OBJECT (%d)
                    1: AST_TYPE
                        flags: TYPE_ARRAY (%d)
                    2: AST_TYPE
                        flags: TYPE_DOUBLE (%d)
                    3: AST_TYPE
                        flags: TYPE_LONG (%d)
                    4: AST_TYPE
                        flags: TYPE_STRING (%d)
                    5: AST_TYPE
                        flags: TYPE_NULL (%d)
                name: "a"
                default: null
        stmts: AST_STMT_LIST
            0: AST_RETURN
                expr: AST_CALL
                    expr: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "json_encode"
                    args: AST_ARG_LIST
                        0: AST_VAR
                            flags: 0
                            name: "a"
        returnType: AST_TYPE_UNION
            0: AST_TYPE
                flags: TYPE_STRING (%d)
            1: AST_TYPE
                flags: TYPE_FALSE (%d)
        __declId: 0
    2: AST_CLASS
        flags: 0
        name: "Xyz"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: AST_TYPE_UNION
                    0: AST_TYPE
                        flags: TYPE_BOOL (%d)
                    1: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "stdClass"
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "x"
                        default: null
                        docComment: null
        __declId: 1
    3: AST_FUNC_DECL
        flags: 0
        name: "testClasses"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_TYPE_UNION
                    0: AST_TYPE
                        flags: TYPE_ITERABLE (%d)
                    1: AST_NAME
                        flags: NAME_FQ (%d)
                        name: "stdClass"
                    2: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "Xyz"
                name: "s"
                default: null
        stmts: AST_STMT_LIST
            0: AST_RETURN
                expr: AST_NEW
                    class: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "X"
                    args: AST_ARG_LIST
        returnType: AST_TYPE_UNION
            0: AST_NAME
                flags: NAME_RELATIVE (%d)
                name: "Xyz"
            1: AST_TYPE
                flags: TYPE_FALSE (%d)
            2: AST_TYPE
                flags: TYPE_NULL (%d)
        __declId: 2
    4: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (%d)
            name: "test"
        args: AST_ARG_LIST
            0: AST_ARRAY
                flags: ARRAY_SYNTAX_SHORT (%d)
    5: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (%d)
            name: "testClasses"
        args: AST_ARG_LIST
            0: AST_ARRAY
                flags: ARRAY_SYNTAX_SHORT (%d)
                0: AST_ARRAY_ELEM
                    flags: 0
                    value: 2
                    key: null
                1: AST_ARRAY_ELEM
                    flags: 0
                    value: 3
                    key: null