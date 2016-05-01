--TEST--
Name nodes
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
foo();
\foo();
('foo')();
('\foo')();
PHP;

echo ast_dump(ast\parse_code($code, $version=30)), "\n";
echo ast_dump(ast\parse_code($code, $version=40)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "foo"
        args: AST_ARG_LIST
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "foo"
        args: AST_ARG_LIST
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "foo"
        args: AST_ARG_LIST
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "\foo"
        args: AST_ARG_LIST
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "foo"
        args: AST_ARG_LIST
    1: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "foo"
        args: AST_ARG_LIST
    2: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "foo"
        args: AST_ARG_LIST
    3: AST_CALL
        expr: AST_NAME
            flags: NAME_FQ (0)
            name: "foo"
        args: AST_ARG_LIST
