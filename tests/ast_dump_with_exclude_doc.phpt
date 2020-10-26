--TEST--
ast_dump() with AST_DUMP_EXCLUDE_DOC_COMMENT
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
function test($foo) {
}
PHP;

$ast = ast\parse_code($code, $version=80);
echo ast_dump($ast, AST_DUMP_EXCLUDE_DOC_COMMENT);

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
                attributes: null
        stmts: AST_STMT_LIST
        returnType: null
        attributes: null
        __declId: 0
