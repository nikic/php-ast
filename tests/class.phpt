--TEST--
Test parse and dump of class
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class A extends B implements D, E {
    use T, S;
    const X = 'Y', Y = 'X';
    public $foo, $bar;
    abstract function test();
}
PHP;

echo ast_dump(ast\parse_code($code, $version=50));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "A"
        docComment: null
        extends: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "B"
        implements: AST_NAME_LIST
            0: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "D"
            1: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "E"
        stmts: AST_STMT_LIST
            0: AST_USE_TRAIT
                traits: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "T"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "S"
                adaptations: null
            1: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "X"
                    value: "Y"
                    docComment: null
                1: AST_CONST_ELEM
                    name: "Y"
                    value: "X"
                    docComment: null
            2: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "foo"
                    default: null
                    docComment: null
                1: AST_PROP_ELEM
                    name: "bar"
                    default: null
                    docComment: null
            3: AST_METHOD
                flags: MODIFIER_PUBLIC | MODIFIER_ABSTRACT (%d)
                name: "test"
                docComment: null
                params: AST_PARAM_LIST
                uses: null
                stmts: null
                returnType: null
                __declId: 0
        __declId: 1
