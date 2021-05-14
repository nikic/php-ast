--TEST--
Enums in php 8.1
--SKIPIF--
<?php if (PHP_VERSION_ID < 80100) die('skip PHP >= 8.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
enum HasValue: int {
    case FOO = 42;
    const FOO_ALIAS = self::FOO;
}

#[MyAttribute(1)]
enum NoValue {
    /** Case doc comment */
    #[OtherAttribute()]
    case FOO;
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=80);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "HasValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: 42
                docComment: null
                attributes: null
            1: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "FOO_ALIAS"
                    value: AST_CLASS_CONST
                        class: AST_NAME
                            flags: NAME_NOT_FQ (%d)
                            name: "self"
                        const: "FOO"
                    docComment: null
        __declId: 0
    1: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "NoValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: null
                docComment: "/** Case doc comment */"
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "OtherAttribute"
                            args: AST_ARG_LIST
        __declId: 1
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "HasValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: 42
                docComment: null
                attributes: null
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (%d)
                const: AST_CLASS_CONST_DECL
                    flags: 0
                    0: AST_CONST_ELEM
                        name: "FOO_ALIAS"
                        value: AST_CLASS_CONST
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "self"
                            const: "FOO"
                        docComment: null
                attributes: null
        attributes: null
        __declId: 0
    1: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "NoValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: null
                docComment: "/** Case doc comment */"
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "OtherAttribute"
                            args: AST_ARG_LIST
        attributes: AST_ATTRIBUTE_LIST
            0: AST_ATTRIBUTE_GROUP
                0: AST_ATTRIBUTE
                    class: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "MyAttribute"
                    args: AST_ARG_LIST
                        0: 1
        __declId: 1
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "HasValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: 42
                docComment: null
                attributes: null
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (%d)
                const: AST_CLASS_CONST_DECL
                    flags: 0
                    0: AST_CONST_ELEM
                        name: "FOO_ALIAS"
                        value: AST_CLASS_CONST
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "self"
                            const: "FOO"
                        docComment: null
                attributes: null
        attributes: null
        type: AST_TYPE
            flags: TYPE_LONG (%d)
        __declId: 0
    1: AST_CLASS
        flags: CLASS_FINAL | CLASS_ENUM (%d)
        name: "NoValue"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_ENUM_CASE
                name: "FOO"
                expr: null
                docComment: "/** Case doc comment */"
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "OtherAttribute"
                            args: AST_ARG_LIST
        attributes: AST_ATTRIBUTE_LIST
            0: AST_ATTRIBUTE_GROUP
                0: AST_ATTRIBUTE
                    class: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "MyAttribute"
                    args: AST_ARG_LIST
                        0: 1
        type: null
        __declId: 1