--TEST--
ast\parse_file() on valid file
--FILE--
<?php

require __DIR__ . '/../util.php';

$ast = ast\parse_file(__DIR__ . '/valid_file.php', $version=30);
echo ast_dump($ast);

?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        expr: AST_ARRAY
            0: AST_ARRAY_ELEM
                flags: 0
                value: 123
                key: null
