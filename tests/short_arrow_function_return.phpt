--TEST--
Arrow functions and types ('fn($x) => $x') in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
static fn() : int => 1;
fn(iterable $i) : array => [$i];
fn(stdClass $param) : \stdClass => $param;
fn(\stdClass $param) : stdClass => $param;
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
    0: AST_ARROW_FUNC
        flags: MODIFIER_STATIC (%d)
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_RETURN
            expr: 1
        returnType: AST_TYPE
            flags: TYPE_LONG (%d)
        __declId: 0
    1: AST_ARROW_FUNC
        flags: 0
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_ITERABLE (%d)
                name: "i"
                default: null
        stmts: AST_RETURN
            expr: AST_ARRAY
                flags: ARRAY_SYNTAX_SHORT (%d)
                0: AST_ARRAY_ELEM
                    flags: 0
                    value: AST_VAR
                        flags: 0
                        name: "i"
                    key: null
        returnType: AST_TYPE
            flags: TYPE_ARRAY (%d)
        __declId: 1
    2: AST_ARROW_FUNC
        flags: 0
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_NOT_FQ (%d)
                    name: "stdClass"
                name: "param"
                default: null
        stmts: AST_RETURN
            expr: AST_VAR
                flags: 0
                name: "param"
        returnType: AST_NAME
            flags: NAME_FQ (%d)
            name: "stdClass"
        __declId: 2
    3: AST_ARROW_FUNC
        flags: 0
        name: "{closure}"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_FQ (%d)
                    name: "stdClass"
                name: "param"
                default: null
        stmts: AST_RETURN
            expr: AST_VAR
                flags: 0
                name: "param"
        returnType: AST_NAME
            flags: NAME_NOT_FQ (%d)
            name: "stdClass"
        __declId: 3

Deprecated: ast\parse_code(): Version 50 is deprecated in %s.php on line 16
Same representation in version 50/70: true