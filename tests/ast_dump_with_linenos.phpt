--TEST--
ast_dump() with AST_DUMP_LINENOS
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip";
?>
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

$ast = ast\parse_code($code, $version=70);
echo ast_dump($ast, AST_DUMP_LINENOS);

?>
--EXPECTF--
AST_STMT_LIST @ 1
    0: AST_FUNC_DECL @ 2-9
        name: "test"
        docComment: null
        params: AST_PARAM_LIST @ 4
        stmts: AST_STMT_LIST @ 5
            0: AST_CALL @ 6
                expr: AST_NAME @ 6
                    flags: NAME_NOT_FQ (%d)
                    name: "var_dump"
                args: AST_ARG_LIST @ 7
                    0: AST_VAR @ 7
                        name: "foo"
        returnType: null
        __declId: 0