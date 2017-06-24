--TEST--
Closures should have unique identifiers within parsed code in version 45
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$fn = static function &($a) use ($b) {
};
$fn2 = function ($a) {
};
PHP;
echo ast_dump(ast\parse_code($code, $version=45));
echo ast_dump(ast\parse_code($code, $version=45));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "fn"
        expr: AST_CLOSURE
            flags: MODIFIER_STATIC | RETURNS_REF (67108865)
            name: {closure}
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
            uses: AST_CLOSURE_USES
                0: AST_CLOSURE_VAR
                    flags: 0
                    name: "b"
            stmts: AST_STMT_LIST
            returnType: null
            __closureId: 0
    1: AST_ASSIGN
        var: AST_VAR
            name: "fn2"
        expr: AST_CLOSURE
            flags: 0
            name: {closure}
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
            uses: null
            stmts: AST_STMT_LIST
            returnType: null
            __closureId: 1AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "fn"
        expr: AST_CLOSURE
            flags: MODIFIER_STATIC | RETURNS_REF (67108865)
            name: {closure}
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
            uses: AST_CLOSURE_USES
                0: AST_CLOSURE_VAR
                    flags: 0
                    name: "b"
            stmts: AST_STMT_LIST
            returnType: null
            __closureId: 0
    1: AST_ASSIGN
        var: AST_VAR
            name: "fn2"
        expr: AST_CLOSURE
            flags: 0
            name: {closure}
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
            uses: null
            stmts: AST_STMT_LIST
            returnType: null
            __closureId: 1

