--TEST--
ast_dump() with AST_DUMP_EXCLUDE_DOC
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip";
if (PHP_VERSION_ID >= 70000 && PHP_VERSION_ID < 70018) print "skip buggy PHP version";
if (PHP_VERSION_ID >= 70100 && PHP_VERSION_ID < 70104) print "skip buggy PHP version";
?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
/**
 * test
 * @param mixed $foo
 * @return void
 */
function
test
($foo)
{
    var_dump(
        $foo
    );
}
PHP;

$ast = ast\parse_code($code, $version=50);
echo ast_dump($ast, AST_DUMP_EXCLUDE_DOC);

?>
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: null
                name: "foo"
                default: null
        uses: null
        stmts: AST_STMT_LIST
            0: AST_CALL
                expr: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "var_dump"
                args: AST_ARG_LIST
                    0: AST_VAR
                        name: "foo"
        returnType: null
        __declId: 0
