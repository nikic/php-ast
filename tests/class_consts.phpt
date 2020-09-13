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

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "Test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "A"
                    value: 1
                    docComment: "/** Doc A */"
            1: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "B"
                    value: 2
                    docComment: null
            2: AST_CLASS_CONST_DECL
                flags: MODIFIER_PROTECTED (%d)
                0: AST_CONST_ELEM
                    name: "C"
                    value: 3
                    docComment: null
            3: AST_CLASS_CONST_DECL
                flags: MODIFIER_PRIVATE (%d)
                0: AST_CONST_ELEM
                    name: "D"
                    value: 4
                    docComment: null
            4: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "E"
                    value: 5
                    docComment: "/** Doc E */"
                1: AST_CONST_ELEM
                    name: "F"
                    value: 6
                    docComment: "/** Doc F */"
        __declId: 0
