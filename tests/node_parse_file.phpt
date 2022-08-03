--TEST--
ast\Node::parseFile() on valid file
--FILE--
<?php

require __DIR__ . '/../util.php';

$ast = ast\Node::parseFile(__DIR__ . '/valid_file.php', $version=50);
echo ast_dump($ast);

?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        expr: 123
