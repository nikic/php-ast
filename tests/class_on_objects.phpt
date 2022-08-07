--TEST--
$x::class in PHP 8.0
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
echo $x::class;
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_VAR
                flags: 0
                name: "x"