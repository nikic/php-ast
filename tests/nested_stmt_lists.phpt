--TEST--
Nested statement lists
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$a;
{
    $b;
    {
        $c;
        {
            $d;
        }
        $e;
    }
    $f;
}
$g;
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
    2: AST_VAR
        flags: 0
        name: "c"
    3: AST_VAR
        flags: 0
        name: "d"
    4: AST_VAR
        flags: 0
        name: "e"
    5: AST_VAR
        flags: 0
        name: "f"
    6: AST_VAR
        flags: 0
        name: "g"