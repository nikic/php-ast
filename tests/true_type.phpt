--TEST--
'true' type parsing
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(true $x): true {
    return $x;
}
PHP;

$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_TRUE (%d)
                name: "x"
                default: null
                attributes: null
                docComment: null
        stmts: AST_STMT_LIST
            0: AST_RETURN
                expr: AST_VAR
                    name: "x"
        returnType: AST_TYPE
            flags: TYPE_TRUE (%d)
        attributes: null
        __declId: 0
