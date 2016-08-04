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

echo ast_dump(ast\parse_code($code, $version=15)), "\n";
echo ast_dump(ast\parse_code($code, $version=20)), "\n";

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 15 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_VAR
        0: "a"
    1: AST_STMT_LIST
        0: AST_VAR
            0: "b"
        1: AST_STMT_LIST
            0: AST_VAR
                0: "c"
            1: AST_STMT_LIST
                0: AST_VAR
                    0: "d"
            2: AST_VAR
                0: "e"
        2: AST_VAR
            0: "f"
    2: AST_VAR
        0: "g"

Deprecated: ast\parse_code(): Version 20 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_VAR
        0: "a"
    1: AST_VAR
        0: "b"
    2: AST_VAR
        0: "c"
    3: AST_VAR
        0: "d"
    4: AST_VAR
        0: "e"
    5: AST_VAR
        0: "f"
    6: AST_VAR
        0: "g"
