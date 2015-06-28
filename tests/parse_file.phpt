--TEST--
ast\parse_file() on valid file
--FILE--
<?php

require __DIR__ . '/../util.php';

$ast = ast\parse_file(__DIR__ . '/valid_file.php');
echo ast_dump($ast);

?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        0: AST_ARRAY
            0: AST_ARRAY_ELEM
                flags: 0
                0: 123
                1: null
