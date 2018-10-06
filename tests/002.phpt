--TEST--
Decl nodes use ast\Node\Decl
--FILE--
<?php

$ast = ast\parse_code('<?php function foo() {}', $version=40);
assert($ast instanceof ast\Node);
assert(!$ast instanceof ast\Node\Decl);
assert($ast->kind == ast\AST_STMT_LIST);

$fn = $ast->children[0];
assert($fn instanceof ast\Node);
assert($fn instanceof ast\Node\Decl);
assert($fn->kind == ast\AST_FUNC_DECL);

?>
===DONE===
--EXPECTF--

Deprecated: ast\parse_code(): Version 40 is deprecated in %s on line %d
===DONE===
