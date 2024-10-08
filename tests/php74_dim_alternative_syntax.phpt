--TEST--
'$x{"offset"}' flag in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400 || PHP_VERSION_ID >= 80400) die('skip PHP 7.4-8.3 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
var_export($x{'offset'});
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_CALL
        expr: AST_NAME
            flags: NAME_NOT_FQ (%d)
            name: "var_export"
        args: AST_ARG_LIST
            0: AST_DIM
                flags: DIM_ALTERNATIVE_SYNTAX (%d)
                expr: AST_VAR
                    name: "x"
                dim: "offset"
