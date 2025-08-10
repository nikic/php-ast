--TEST--
Void cast
--SKIPIF--
<?php if (PHP_VERSION_ID < 80500) die('skip PHP >=8.5 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
(void) foo();
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CAST
        flags: TYPE_VOID (%d)
        expr: AST_CALL
            expr: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "foo"
            args: AST_ARG_LIST
