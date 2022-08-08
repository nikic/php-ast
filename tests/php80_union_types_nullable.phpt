--TEST--
Union types in PHP 8.0 (nullable)
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php

function test(?array $a, ?object $o) : ?\stdClass {
    return null;
}
class X {
    public ?array $arr;
    public ?\ArrayObject $obj;
}
PHP;

$node = ast\parse_code($code, $version=50);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
Deprecated: ast\parse_code(): Version 50 is deprecated in %s.php on line 17
AST_STMT_LIST
    0: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ARRAY (7)
                name: "a"
                default: null
            1: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_OBJECT (8)
                name: "o"
                default: null
        uses: null
        stmts: AST_STMT_LIST
            0: AST_RETURN
                expr: AST_CONST
                    name: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "null"
        returnType: AST_NULLABLE_TYPE
            type: AST_NAME
                flags: NAME_FQ (0)
                name: "stdClass"
        __declId: 0
    1: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (1)
                0: AST_PROP_ELEM
                    name: "arr"
                    default: null
                    docComment: null
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (1)
                0: AST_PROP_ELEM
                    name: "obj"
                    default: null
                    docComment: null
        __declId: 1
AST_STMT_LIST
    0: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ARRAY (7)
                name: "a"
                default: null
            1: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_OBJECT (8)
                name: "o"
                default: null
        stmts: AST_STMT_LIST
            0: AST_RETURN
                expr: AST_CONST
                    name: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "null"
        returnType: AST_NULLABLE_TYPE
            type: AST_NAME
                flags: NAME_FQ (0)
                name: "stdClass"
        __declId: 0
    1: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ARRAY (7)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "arr"
                        default: null
                        docComment: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_NULLABLE_TYPE
                    type: AST_NAME
                        flags: NAME_FQ (0)
                        name: "ArrayObject"
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "obj"
                        default: null
                        docComment: null
        __declId: 1