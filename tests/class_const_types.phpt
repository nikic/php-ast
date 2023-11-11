--TEST--
PHP 8.3 class constant types
--SKIPIF--
<?php if (PHP_VERSION_ID < 80300) die('skip PHP >= 8.3 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Test {
    public const int X = 1, Y = 2;
    const ?Foo BAR = XYZ;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=90));
echo "\n\nIn version 100\n";
echo ast_dump(ast\parse_code($code, $version=100));
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        name: "Test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (1)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "X"
                        value: 1
                        docComment: null
                    1: AST_CONST_ELEM
                        name: "Y"
                        value: 2
                        docComment: null
                attributes: null
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (1)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "BAR"
                        value: AST_CONST
                            name: AST_NAME
                                flags: NAME_NOT_FQ (1)
                                name: "XYZ"
                        docComment: null
                attributes: null
        attributes: null
        type: null
        __declId: 0

In version 100
AST_STMT_LIST
    0: AST_CLASS
        name: "Test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (1)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "X"
                        value: 1
                        docComment: null
                    1: AST_CONST_ELEM
                        name: "Y"
                        value: 2
                        docComment: null
                attributes: null
                type: AST_TYPE
                    flags: TYPE_LONG (4)
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (1)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "BAR"
                        value: AST_CONST
                            name: AST_NAME
                                flags: NAME_NOT_FQ (1)
                                name: "XYZ"
                        docComment: null
                attributes: null
                type: AST_NULLABLE_TYPE
                    type: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "Foo"
        attributes: null
        type: null
        __declId: 0
