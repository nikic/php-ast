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

enum NoValue {
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
                0: null
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
                0: null
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
                0: null
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
                0: null
        attributes: null
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
                0: null
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
                0: null
        attributes: null
        __declId: 1