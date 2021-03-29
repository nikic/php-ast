--TEST--
Class types only used for enums in AST version 85
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
final class X {
}

interface I {
}
PHP;

$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_FINAL (%d)
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        attributes: null
        type: null
        __declId: 0
    1: AST_CLASS
        flags: CLASS_INTERFACE (%d)
        name: "I"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        attributes: null
        type: null
        __declId: 1