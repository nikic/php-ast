--TEST--
ast\Node and ast\Node\Decl have __construct defined
--FILE--
<?php

require __DIR__ . '/../util.php';
error_reporting(E_ALL);

// Parameters are optional
var_dump(new \ast\Node());
var_dump(new \ast\Node\Decl());

// Check that null can be passed to any nullable param
// ?int $kind, ?int $flags, ?array $children, ?int $lineno, ?int $endLineno]
var_dump(new \ast\Node(null, null, null, null));
var_dump(new \ast\Node\Decl(null, null, null, null));

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
object(ast\Node\Decl)#1 (7) {
  ["kind"]=>
  NULL
  ["flags"]=>
  NULL
  ["lineno"]=>
  NULL
  ["children"]=>
  NULL
  ["endLineno"]=>
  NULL
  ["name"]=>
  NULL
  ["docComment"]=>
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
object(ast\Node\Decl)#1 (7) {
  ["kind"]=>
  NULL
  ["flags"]=>
  NULL
  ["lineno"]=>
  NULL
  ["children"]=>
  NULL
  ["endLineno"]=>
  NULL
  ["name"]=>
  NULL
  ["docComment"]=>
  NULL
}
AST_NAME @ 2
    flags: NAME_NOT_FQ (1)
    name: "FOO"
===DONE===
