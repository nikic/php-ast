--TEST--
ast\parse_file() on valid file
--FILE--
<?php

require __DIR__ . '/../util.php';

$ast = ast\parse_file(__DIR__ . '/valid_file.php', $version=70);
echo ast_dump($ast);

?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        expr: 123
