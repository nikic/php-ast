--TEST--
Simple clone
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
clone $foo;
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=120);
echo ast_dump($node), "\n";
--EXPECT--
AST_STMT_LIST
    0: AST_CLONE
        expr: AST_VAR
            name: "foo"
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "clone"
        args: AST_ARG_LIST
            0: AST_VAR
                name: "foo"
