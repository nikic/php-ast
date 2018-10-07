--TEST--
ast\Node has a __construct method
--FILE--
<?php

require __DIR__ . '/../util.php';
error_reporting(E_ALL);

// Parameters are optional
var_dump(new \ast\Node());

// Check that null can be passed to any nullable param
// ?int $kind, ?int $flags, ?array $children, ?int $lineno, ?int $endLineno]
var_dump(new \ast\Node(null, null, null, null));

// `FOO`
echo ast_dump(new \ast\Node(\ast\AST_NAME, 1, ['name' => 'FOO'], 2), AST_DUMP_LINENOS) . "\n";

?>
===DONE===
--EXPECT--
object(ast\Node)#1 (4) {
  ["kind"]=>
  NULL
  ["flags"]=>
  NULL
  ["lineno"]=>
  NULL
  ["children"]=>
  NULL
}
object(ast\Node)#1 (4) {
  ["kind"]=>
  NULL
  ["flags"]=>
  NULL
  ["lineno"]=>
  NULL
  ["children"]=>
  NULL
}
AST_NAME @ 2
    flags: NAME_NOT_FQ (1)
    name: "FOO"
===DONE===
