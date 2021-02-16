--TEST--
Function parameters
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test($a, ...$b) {}
function test2(&$a, &...$b) {}
function test3(array &$a, array &...$b) {}
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";
echo ast_dump(ast\parse_code($code, $version=80)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: null
                name: "a"
                default: null
            1: AST_PARAM
                flags: PARAM_VARIADIC (%d)
                type: null
                name: "b"
                default: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 0
    1: AST_FUNC_DECL
        flags: 0
        name: "test2"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: PARAM_REF (%d)
                type: null
                name: "a"
                default: null
            1: AST_PARAM
                flags: PARAM_REF | PARAM_VARIADIC (%d)
                type: null
                name: "b"
                default: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 1
    2: AST_FUNC_DECL
        flags: 0
        name: "test3"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: PARAM_REF (%d)
                type: AST_TYPE
                    flags: TYPE_ARRAY (%d)
                name: "a"
                default: null
            1: AST_PARAM
                flags: PARAM_REF | PARAM_VARIADIC (%d)
                type: AST_TYPE
                    flags: TYPE_ARRAY (%d)
                name: "b"
                default: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 2
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: null
                name: "a"
                default: null
                attributes: null
                docComment: null
            1: AST_PARAM
                flags: PARAM_VARIADIC (%d)
                type: null
                name: "b"
                default: null
                attributes: null
                docComment: null
        stmts: AST_STMT_LIST
        returnType: null
        attributes: null
        __declId: 0
    1: AST_FUNC_DECL
        flags: 0
        name: "test2"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: PARAM_REF (%d)
                type: null
                name: "a"
                default: null
                attributes: null
                docComment: null
            1: AST_PARAM
                flags: PARAM_REF | PARAM_VARIADIC (%d)
                type: null
                name: "b"
                default: null
                attributes: null
                docComment: null
        stmts: AST_STMT_LIST
        returnType: null
        attributes: null
        __declId: 1
    2: AST_FUNC_DECL
        flags: 0
        name: "test3"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: PARAM_REF (%d)
                type: AST_TYPE
                    flags: TYPE_ARRAY (%d)
                name: "a"
                default: null
                attributes: null
                docComment: null
            1: AST_PARAM
                flags: PARAM_REF | PARAM_VARIADIC (%d)
                type: AST_TYPE
                    flags: TYPE_ARRAY (%d)
                name: "b"
                default: null
                attributes: null
                docComment: null
        stmts: AST_STMT_LIST
        returnType: null
        attributes: null
        __declId: 2