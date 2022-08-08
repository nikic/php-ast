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
--EXPECTF--

Deprecated: ast\parse_code(): Version 50 is deprecated in %s.php on line 11
AST_STMT_LIST
    0: AST_CLOSURE
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    1: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 1

Deprecated: ast\parse_code(): Version 60 is deprecated in %s.php on line 12
AST_STMT_LIST
    0: AST_CLOSURE
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    1: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 1
