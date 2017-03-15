--TEST--
Nested statement lists
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip ast extension not loaded";
if (!extension_loaded("tokenizer")) print "skip tokenizer extension not loaded";
?>
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

echo ast_dump(ast\parse_code($code, $version=40)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_VAR
        name: "a"
    1: AST_VAR
        name: "b"
    2: AST_VAR
        name: "c"
    3: AST_VAR
        name: "d"
    4: AST_VAR
        name: "e"
    5: AST_VAR
        name: "f"
    6: AST_VAR
        name: "g"
