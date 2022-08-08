--TEST--
The object type is recognized
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(object $obj) : object {}
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_TYPE
                    flags: TYPE_OBJECT (%d)
                name: "obj"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_TYPE
            flags: TYPE_OBJECT (%d)
        __declId: 0
