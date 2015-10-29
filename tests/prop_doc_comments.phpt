--TEST--
Doc comments on properties
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
class A {
    /** docComment $a */
    public $a;

    public
        /** docComment $b */
        $b,
        /** docComment $c */
        $c
    ;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=10)), "\n";
echo ast_dump(ast\parse_code($code, $version=15)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        0: null
        1: null
        2: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                docComment: /** docComment $a */
                0: AST_PROP_ELEM
                    0: "a"
                    1: null
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                docComment: /** docComment $b */
                0: AST_PROP_ELEM
                    0: "b"
                    1: null
                1: AST_PROP_ELEM
                    0: "c"
                    1: null
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        0: null
        1: null
        2: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_PROP_ELEM
                    docComment: /** docComment $a */
                    0: "a"
                    1: null
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_PROP_ELEM
                    docComment: /** docComment $b */
                    0: "b"
                    1: null
                1: AST_PROP_ELEM
                    docComment: /** docComment $c */
                    0: "c"
                    1: null
