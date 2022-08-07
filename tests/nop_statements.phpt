--TEST--
Nop statements
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a;
;
$b;
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_VAR
        flags: 0
        name: "a"
    1: AST_VAR
        flags: 0
        name: "b"
