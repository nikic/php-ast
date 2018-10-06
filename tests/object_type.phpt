--TEST--
The object type is recognized
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(object $obj) : object {}
PHP;

echo ast_dump(ast\parse_code($code, $version=40)), "\n";
echo ast_dump(ast\parse_code($code, $version=45));

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 40 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: test
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "object"
                name: "obj"
                default: null
        uses: null
        stmts: AST_STMT_LIST
        returnType: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "object"
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: test
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_OBJECT (8)
                name: "obj"
                default: null
        uses: null
        stmts: AST_STMT_LIST
        returnType: AST_TYPE
            flags: TYPE_OBJECT (8)
