--TEST--
ast\parse_file() with empty file
--FILE--
<?php
require __DIR__ . '/../util.php';

$file = ast\parse_file(__DIR__ . '/empty_file.php', $version=50);
var_dump($file instanceof ast\Node);
echo ast_dump($file) . "\n";
$file = ast\parse_code('', $version=50);
var_dump($file instanceof ast\Node);
echo ast_dump($file) . "\n";
?>
--EXPECT--
bool(true)
AST_STMT_LIST
bool(true)
AST_STMT_LIST
