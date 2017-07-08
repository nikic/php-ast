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

echo "Version 45\n";
$file_45 = ast\parse_file(__DIR__ . '/empty_file.php', $version=45);
var_dump($file_45 instanceof ast\Node);
echo ast_dump($file_45) . "\n";
$file_45 = ast\parse_code('', $version=45);
var_dump($file_45 instanceof ast\Node);
echo ast_dump($file_45) . "\n";
?>
--EXPECT--
Version 40
NULL
bool(true)
AST_STMT_LIST
Version 45
bool(true)
AST_STMT_LIST
bool(true)
AST_STMT_LIST
