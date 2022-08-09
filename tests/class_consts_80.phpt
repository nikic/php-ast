--TEST--
Class constants in AST version 80
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Test {
    /** Doc A */
    const A = 1;
    protected const
        /** Doc E */
        E = 5,
        /** Doc F */
        F = 6;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=80));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "Test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PUBLIC (%d)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "A"
                        value: 1
                        docComment: "/** Doc A */"
                attributes: null
            1: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PROTECTED (%d)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "E"
                        value: 5
                        docComment: "/** Doc E */"
                    1: AST_CONST_ELEM
                        name: "F"
                        value: 6
                        docComment: "/** Doc F */"
                attributes: null
        attributes: null
        __declId: 0
