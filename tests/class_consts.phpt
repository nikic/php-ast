--TEST--
Class constants
--SKIPIF--
<?php if (PHP_VERSION_ID < 70100) die('skip PHP >= 7.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Test {
    /** Doc A */
    const A = 1;
    public const B = 2;
    protected const C = 3;
    private const D = 4;
    const
        /** Doc E */
        E = 5,
        /** Doc F */
        F = 6;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=30));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: Test
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_CONST_ELEM
                    docComment: /** Doc A */
                    name: "A"
                    value: 1
            1: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_CONST_ELEM
                    name: "B"
                    value: 2
            2: AST_CLASS_CONST_DECL
                flags: MODIFIER_PROTECTED (512)
                0: AST_CONST_ELEM
                    name: "C"
                    value: 3
            3: AST_CLASS_CONST_DECL
                flags: MODIFIER_PRIVATE (1024)
                0: AST_CONST_ELEM
                    name: "D"
                    value: 4
            4: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (256)
                0: AST_CONST_ELEM
                    docComment: /** Doc E */
                    name: "E"
                    value: 5
                1: AST_CONST_ELEM
                    docComment: /** Doc F */
                    name: "F"
                    value: 6
