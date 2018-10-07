--TEST--
The object type is recognized
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(object $obj) : object {}
PHP;

echo ast_dump(ast\parse_code($code, $version=60));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_OBJECT (8)
                name: "obj"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_TYPE
            flags: TYPE_OBJECT (8)
        __declId: 0
