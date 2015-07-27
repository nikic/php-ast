--TEST--
eval() and include parsing
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
eval( 'echo "hello";');
include 'foo.php';
include_once 'foo.php';
require 'foo.php';
require_once 'foo.php';
PHP;

echo ast_dump(ast\parse_code($code));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_INCLUDE_OR_EVAL
        flags: EXEC_EVAL (1)
        0: "echo "hello";"
    1: AST_INCLUDE_OR_EVAL
        flags: EXEC_INCLUDE (2)
        0: "foo.php"
    2: AST_INCLUDE_OR_EVAL
        flags: EXEC_INCLUDE_ONCE (4)
        0: "foo.php"
    3: AST_INCLUDE_OR_EVAL
        flags: EXEC_REQUIRE (8)
        0: "foo.php"
    4: AST_INCLUDE_OR_EVAL
        flags: EXEC_REQUIRE_ONCE (16)
        0: "foo.php"
