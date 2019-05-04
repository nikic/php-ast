--TEST--
Arrow functions ('fn($x) => $x') in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$y = 1;
$a = fn($x) => $x * $y;
$b = static fn() => 1;
$c = /** doc comment */ static fn(?int... $args): array => $args;
$fn = fn() => yield 123;
PHP;

$node = ast\parse_code($code, $version=70);
$version_70_repr = ast_dump($node);
echo $version_70_repr . "\n";
$node50 = ast\parse_code($code, $version=50);
$version_50_repr = ast_dump($node50);
echo "Same representation in version 50/70: ";
var_export($version_50_repr == $version_70_repr);
echo "\n";
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "y"
        expr: 1
    1: AST_ASSIGN
        var: AST_VAR
            name: "a"
        expr: AST_ARROW_FUNC
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "x"
                    default: null
            stmts: AST_RETURN
                expr: AST_BINARY_OP
                    flags: BINARY_MUL (3)
                    left: AST_VAR
                        name: "x"
                    right: AST_VAR
                        name: "y"
            returnType: null
            __declId: 0
    2: AST_ASSIGN
        var: AST_VAR
            name: "b"
        expr: AST_ARROW_FUNC
            flags: MODIFIER_STATIC (16)
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
            stmts: AST_RETURN
                expr: 1
            returnType: null
            __declId: 1
    3: AST_ASSIGN
        var: AST_VAR
            name: "c"
        expr: AST_ARROW_FUNC
            flags: MODIFIER_STATIC (16)
            name: "{closure}"
            docComment: "/** doc comment */"
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: PARAM_VARIADIC (2)
                    type: AST_NULLABLE_TYPE
                        type: AST_TYPE
                            flags: TYPE_LONG (4)
                    name: "args"
                    default: null
            stmts: AST_RETURN
                expr: AST_VAR
                    name: "args"
            returnType: AST_TYPE
                flags: TYPE_ARRAY (7)
            __declId: 2
    4: AST_ASSIGN
        var: AST_VAR
            name: "fn"
        expr: AST_ARROW_FUNC
            flags: FUNC_GENERATOR (16777216)
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
            stmts: AST_RETURN
                expr: AST_YIELD
                    value: 123
                    key: null
            returnType: null
            __declId: 3
Same representation in version 50/70: true