--TEST--
ast_dump() with AST_DUMP_LINENOS
--SKIPIF--
<?php if (!extension_loaded("ast")) print "skip"; ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function
test
()
{
    var_dump(
        $foo
    );
}
PHP;

$ast = ast\parse_code($code);
echo ast_dump($ast, AST_DUMP_LINENOS);

// The lineno for AST_ARG_LIST is wrong...

?>
--EXPECT--
AST_STMT_LIST @ 1
    0: AST_FUNC_DECL @ 2-9
        flags: 0
        name: test
        0: AST_PARAM_LIST @ 4
        1: null
        2: AST_STMT_LIST @ 5
            0: AST_CALL @ 6
                0: AST_NAME @ 6
                    flags: 1
                    0: "var_dump"
                1: AST_ARG_LIST @ 8
                    0: AST_VAR @ 7
                        0: "foo"
        3: null
