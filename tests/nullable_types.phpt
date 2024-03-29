--TEST--
Nullable types
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(?Foo $foo) : ?Bar {
}
function test(?int $foo) : ?int {
}
function test(?array $foo) : ?array {
}
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "Foo"
                name: "foo"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "Bar"
        __declId: 0
    1: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_LONG (4)
                name: "foo"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_TYPE
                flags: TYPE_LONG (4)
        __declId: 1
    2: AST_FUNC_DECL
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ARRAY (7)
                name: "foo"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_TYPE
                flags: TYPE_ARRAY (7)
        __declId: 2
