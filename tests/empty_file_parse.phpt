--TEST--
ast\parse_file() with empty file
--FILE--
<?php
require __DIR__ . '/../util.php';

echo "Version 40\n";
$file = ast\parse_file(__DIR__ . '/empty_file.php', $version=40);
var_dump($file);
$file = ast\parse_code('', $version=40);
var_dump($file instanceof ast\Node);
echo ast_dump($file) . "\n";

echo "Version 50\n";
$file_50 = ast\parse_file(__DIR__ . '/empty_file.php', $version=50);
var_dump($file_50 instanceof ast\Node);
echo ast_dump($file_50) . "\n";
$file_50 = ast\parse_code('', $version=50);
var_dump($file_50 instanceof ast\Node);
echo ast_dump($file_50) . "\n";
?>
--EXPECT--
Version 40
NULL
bool(true)
AST_STMT_LIST
Version 50
bool(true)
AST_STMT_LIST
bool(true)
AST_STMT_LIST
