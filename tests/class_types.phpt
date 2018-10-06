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

echo ast_dump(ast\parse_code($code, $version=40));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        extends: null
        implements: null
        stmts: AST_STMT_LIST
    1: AST_CLASS
        flags: CLASS_ABSTRACT (%d)
        name: B
        extends: null
        implements: null
        stmts: AST_STMT_LIST
    2: AST_CLASS
        flags: CLASS_FINAL (%d)
        name: C
        extends: null
        implements: null
        stmts: AST_STMT_LIST
    3: AST_CLASS
        flags: CLASS_TRAIT (%d)
        name: D
        extends: null
        implements: null
        stmts: AST_STMT_LIST
    4: AST_CLASS
        flags: CLASS_INTERFACE (%d)
        name: E
        extends: null
        implements: null
        stmts: AST_STMT_LIST
    5: AST_NEW
        class: AST_CLASS
            flags: CLASS_ANONYMOUS (%d)
            extends: null
            implements: null
            stmts: AST_STMT_LIST
        args: AST_ARG_LIST
