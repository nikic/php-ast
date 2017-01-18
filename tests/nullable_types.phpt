--TEST--
Nullable types
--SKIPIF--
<?php if (PHP_VERSION_ID < 70100) die('skip PHP >= 7.1 only'); ?>
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

echo ast_dump(ast\parse_code($code, $version=40));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: test
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NULLABLE_TYPE
                    type: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "Foo"
                name: "foo"
                default: null
        uses: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "Bar"
    1: AST_FUNC_DECL
        flags: 0
        name: test
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_LONG (4)
                name: "foo"
                default: null
        uses: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_TYPE
                flags: TYPE_LONG (4)
    2: AST_FUNC_DECL
        flags: 0
        name: test
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ARRAY (7)
                name: "foo"
                default: null
        uses: null
        stmts: AST_STMT_LIST
        returnType: AST_NULLABLE_TYPE
            type: AST_TYPE
                flags: TYPE_ARRAY (7)
