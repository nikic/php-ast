--TEST--
Class properties in AST version 70 (php 7.0+)
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace Foo;
class test {
    public $i = 2, $j;
    protected $row;
    var $o;
    private static $normal = null;
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "Foo"
        stmts: null
    1: AST_CLASS
        name: "test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "i"
                        default: 2
                        docComment: null
                    1: AST_PROP_ELEM
                        name: "j"
                        default: null
                        docComment: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PROTECTED (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "row"
                        default: null
                        docComment: null
            2: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "o"
                        default: null
                        docComment: null
            3: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_STATIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "normal"
                        default: AST_CONST
                            name: AST_NAME
                                flags: NAME_NOT_FQ (1)
                                name: "null"
                        docComment: null
        __declId: 0
