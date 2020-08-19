--TEST--
Attributes in PHP 8.0 on classes
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

// Some edge cases in parsing attributes to compare other parsers against
$code = <<<'PHP'
<?php
namespace NS;

#[\SomeAttribute()]
class X {
    #[Attr1]
    #[
        Attr2(true), # Line comment on an attribute
    ]
    public $prop;

    #[Attr3]
    public const CONST_WITH_ATTRIBUTE = 123;

    #[Attr4, Attr5()]
    public static function hasAttribute(
        #[ThisIsAnAttribute, \AnotherAttribute] $parameter
    ) {}
}
#[Deprecated]
function myGlobal() {}
PHP;

echo ast_dump(ast\parse_code($code, $version=70));
echo "\nIn version 80\n";
echo ast_dump(ast\parse_code($code, $version=80));
--EXPECTF--
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "NS"
        stmts: null
    1: AST_CLASS
        flags: 0
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "prop"
                        default: null
                        docComment: null
            1: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "CONST_WITH_ATTRIBUTE"
                    value: 123
                    docComment: null
            2: AST_METHOD
                flags: MODIFIER_PUBLIC | MODIFIER_STATIC (%d)
                name: "hasAttribute"
                docComment: null
                params: AST_PARAM_LIST
                    0: AST_PARAM
                        flags: 0
                        type: null
                        name: "parameter"
                        default: null
                stmts: AST_STMT_LIST
                returnType: null
                __declId: 0
        __declId: 1
    2: AST_FUNC_DECL
        flags: 0
        name: "myGlobal"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 2
In version 80
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "NS"
        stmts: null
    1: AST_CLASS
        flags: 0
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "prop"
                        default: null
                        docComment: null
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Attr1"
                            args: null
                    1: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Attr2"
                            args: AST_ARG_LIST
                                0: AST_CONST
                                    name: AST_NAME
                                        flags: NAME_NOT_FQ (%d)
                                        name: "true"
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (%d)
                const: AST_CLASS_CONST_DECL
                    flags: 0
                    0: AST_CONST_ELEM
                        name: "CONST_WITH_ATTRIBUTE"
                        value: 123
                        docComment: null
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Attr3"
                            args: null
            2: AST_METHOD
                flags: MODIFIER_PUBLIC | MODIFIER_STATIC (%d)
                name: "hasAttribute"
                docComment: null
                params: AST_PARAM_LIST
                    0: AST_PARAM
                        flags: 0
                        type: null
                        name: "parameter"
                        default: null
                        attributes: AST_ATTRIBUTE_LIST
                            0: AST_ATTRIBUTE_GROUP
                                0: AST_ATTRIBUTE
                                    class: AST_NAME
                                        flags: NAME_NOT_FQ (%d)
                                        name: "ThisIsAnAttribute"
                                    args: null
                                1: AST_ATTRIBUTE
                                    class: AST_NAME
                                        flags: NAME_FQ (%d)
                                        name: "AnotherAttribute"
                                    args: null
                        docComment: null
                stmts: AST_STMT_LIST
                returnType: null
                attributes: AST_ATTRIBUTE_LIST
                    0: AST_ATTRIBUTE_GROUP
                        0: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Attr4"
                            args: null
                        1: AST_ATTRIBUTE
                            class: AST_NAME
                                flags: NAME_NOT_FQ (%d)
                                name: "Attr5"
                            args: AST_ARG_LIST
                __declId: 0
        attributes: null
        __declId: 1
    2: AST_FUNC_DECL
        flags: 0
        name: "myGlobal"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: null
        attributes: AST_ATTRIBUTE_LIST
            0: AST_ATTRIBUTE_GROUP
                0: AST_ATTRIBUTE
                    class: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "Deprecated"
                    args: null
        __declId: 2