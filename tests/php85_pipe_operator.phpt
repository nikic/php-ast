--TEST--
Pipe operator
--SKIPIF--
<?php if (PHP_VERSION_ID < 80500) die('skip PHP >=8.5 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a |> $b;
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_BINARY_OP
        flags: BINARY_PIPE (%d)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"
