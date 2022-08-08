--TEST--
Final class constants
--FILE--
<?php

require __DIR__ . '/../util.php';

// In older php versions, this is allowed by the parser but forbidden by the compiler.
$code = <<<'PHP'
<?php
class X {
    final private const Y = 1;
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=80);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PRIVATE | MODIFIER_FINAL (%d)
                0: AST_CONST_ELEM
                    name: "Y"
                    value: 1
                    docComment: null
        __declId: 0
AST_STMT_LIST
    0: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_FINAL (%d)
                const: AST_CLASS_CONST_DECL
                    0: AST_CONST_ELEM
                        name: "Y"
                        value: 1
                        docComment: null
                attributes: null
        attributes: null
        __declId: 0
