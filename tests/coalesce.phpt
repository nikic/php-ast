--TEST--
Null-coalesce operator
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a ?? $b;
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_BINARY_OP
        flags: BINARY_COALESCE (%d)
        left: AST_VAR
            name: "a"
        right: AST_VAR
            name: "b"