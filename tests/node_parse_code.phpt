--TEST--
Node::parseCode() on valid code
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php

return 123;
PHP;

echo ast_dump(ast\Node::parseCode($code, $version=50)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        expr: 123
