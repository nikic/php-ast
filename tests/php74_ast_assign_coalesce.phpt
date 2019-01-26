--TEST--
'??=' operator in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$x ??= 2;
Foo::$prop['offset'] ??= $other ??= 'value';
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN_OP
        flags: BINARY_COALESCE (%d)
        var: AST_VAR
            name: "x"
        expr: 2
    1: AST_ASSIGN_OP
        flags: BINARY_COALESCE (%d)
        var: AST_DIM
            expr: AST_STATIC_PROP
                class: AST_NAME
                    flags: NAME_NOT_FQ (%d)
                    name: "Foo"
                prop: "prop"
            dim: "offset"
        expr: AST_ASSIGN_OP
            flags: BINARY_COALESCE (%d)
            var: AST_VAR
                name: "other"
            expr: "value"
