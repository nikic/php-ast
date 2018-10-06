--TEST--
Uses only make sense on closures
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function() {};
function test() {}
PHP;

echo ast_dump(ast\parse_code($code, $version=50)), "\n";
echo ast_dump(ast\parse_code($code, $version=60)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_CLOSURE
        flags: 0
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    1: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 1
AST_STMT_LIST
    0: AST_CLOSURE
        flags: 0
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    1: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 1
