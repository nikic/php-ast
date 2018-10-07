--TEST--
ast\parse_file() with empty file
--FILE--
<?php
require __DIR__ . '/../util.php';

echo "Version 45\n";
$file = ast\parse_file(__DIR__ . '/empty_file.php', $version=45);
var_dump($file);
$file = ast\parse_code('', $version=45);
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
--EXPECTF--
Version 45

Deprecated: ast\parse_file(): Version 45 is deprecated in %s on line %d
NULL

Deprecated: ast\parse_code(): Version 45 is deprecated in %s on line %d
bool(true)
AST_STMT_LIST
Version 50
bool(true)
AST_STMT_LIST
bool(true)
AST_STMT_LIST
