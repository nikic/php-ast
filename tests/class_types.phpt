--TEST--
Different class types
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
class A {}
abstract class B {}
final class C {}
trait D {}
interface E {}
new class {};
PHP;

echo ast_dump(ast\parse_code($code, $version=20));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        0: null
        1: null
        2: AST_STMT_LIST
    1: AST_CLASS
        flags: CLASS_ABSTRACT (32)
        name: B
        0: null
        1: null
        2: AST_STMT_LIST
    2: AST_CLASS
        flags: CLASS_FINAL (4)
        name: C
        0: null
        1: null
        2: AST_STMT_LIST
    3: AST_CLASS
        flags: CLASS_TRAIT (128)
        name: D
        0: null
        1: null
        2: AST_STMT_LIST
    4: AST_CLASS
        flags: CLASS_INTERFACE (64)
        name: E
        0: null
        1: null
        2: AST_STMT_LIST
    5: AST_NEW
        0: AST_CLASS
            flags: CLASS_ANONYMOUS (256)
            0: null
            1: null
            2: AST_STMT_LIST
        1: AST_ARG_LIST
