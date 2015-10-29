--TEST--
Test parse and dump of class
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip ast extension not loaded";
if (!extension_loaded("tokenizer")) print "skip tokenizer extension not loaded";
?>
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

echo ast_dump(ast\parse_code($code, $version=15));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        0: AST_NAME
            flags: NAME_NOT_FQ (1)
            0: "B"
        1: AST_NAME_LIST
            0: AST_NAME
                flags: NAME_NOT_FQ (1)
                0: "D"
            1: AST_NAME
                flags: NAME_NOT_FQ (1)
                0: "E"
        2: AST_STMT_LIST
            0: AST_USE_TRAIT
                0: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        0: "T"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        0: "S"
                1: null
            1: AST_CLASS_CONST_DECL
                0: AST_CONST_ELEM
                    0: "X"
                    1: "Y"
                1: AST_CONST_ELEM
                    0: "Y"
                    1: "X"
            2: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_PROP_ELEM
                    0: "foo"
                    1: null
                1: AST_PROP_ELEM
                    0: "bar"
                    1: null
            3: AST_METHOD
                flags: MODIFIER_PUBLIC | MODIFIER_ABSTRACT (258)
                name: test
                0: AST_PARAM_LIST
                1: null
                2: null
                3: null
