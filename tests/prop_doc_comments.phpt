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

echo ast_dump(ast\parse_code($code, $version=40)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_PROP_ELEM
                    docComment: /** docComment $a */
                    name: "a"
                    default: null
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_PROP_ELEM
                    docComment: /** docComment $b */
                    name: "b"
                    default: null
                1: AST_PROP_ELEM
                    docComment: /** docComment $c */
                    name: "c"
                    default: null
