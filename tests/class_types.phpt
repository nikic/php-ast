--TEST--
Different class types
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class A {}
abstract class B {}
final class C {}
trait D {}
interface E {}
new class {};
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        __declId: 0
    1: AST_CLASS
        flags: CLASS_ABSTRACT (%d)
        name: "B"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        __declId: 1
    2: AST_CLASS
        flags: CLASS_FINAL (%d)
        name: "C"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        __declId: 2
    3: AST_CLASS
        flags: CLASS_TRAIT (%d)
        name: "D"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        __declId: 3
    4: AST_CLASS
        flags: CLASS_INTERFACE (%d)
        name: "E"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        __declId: 4
    5: AST_NEW
        class: AST_CLASS
            flags: CLASS_ANONYMOUS (%d)
            name: null
            docComment: null
            extends: null
            implements: null
            stmts: AST_STMT_LIST
            __declId: 5
        args: AST_ARG_LIST
