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
var_dump(new \ast\Node(null, null, null, null, null));
// ?int $kind, ?int $flags, ?array $children, ?int $lineno, ?int $endlineno, ?string $name, ?string $docComment
var_dump(new \ast\Node\Decl(null, null, null, null, null, null, null));

// `FOO`
echo ast_dump(new \ast\Node(\ast\AST_NAME, 1, ['name' => 'FOO'], 2), AST_DUMP_LINENOS) . "\n";
// int $kind, int $flags, array $children, int $lineno, int $endlineno, ?string $name, ?string $docComment
// `/** comment */ class X{\n}`
$classNode = new \ast\Node\Decl(\ast\AST_CLASS, 0, [
    new \ast\Node(\ast\AST_STMT_LIST, 0, [], 1)], 1, 2, 'X', '/** comment */'
);
echo ast_dump($classNode, AST_DUMP_LINENOS) . "\n";
// Test passing some but not all expected params
var_dump(new \ast\Node\Decl(0, 0));
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
AST_CLASS @ 1-2
    flags: 0
    name: X
    docComment: /** comment */
    0: AST_STMT_LIST @ 1
object(ast\Node\Decl)#3 (7) {
  ["kind"]=>
  int(0)
  ["flags"]=>
  int(0)
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
===DONE===
