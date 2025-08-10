--TEST--
Simple exit
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
exit($foo);
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=120);
echo ast_dump($node), "\n";
--EXPECT--
AST_STMT_LIST
    0: AST_EXIT
        expr: AST_VAR
            name: "foo"
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "exit"
        args: AST_ARG_LIST
            0: AST_VAR
                name: "foo"
