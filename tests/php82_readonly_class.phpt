--TEST--
readonly class support in php 8.2+
--SKIPIF--
<?php if (PHP_VERSION_ID < 80200) die('skip PHP >=8.2 only'); ?>
--FILE--
<?php
require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
readonly class X {}
PHP;

$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_READONLY (%d)
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
        attributes: null
        type: null
        __declId: 0
