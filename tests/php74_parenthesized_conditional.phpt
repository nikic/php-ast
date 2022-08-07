--TEST--
parenthesized conditionals in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
return $a ? $b : $c ? $d : $e;
return $a ? $b : ($c ? $d : $e);
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECT--
AST_STMT_LIST
    0: AST_RETURN
        expr: AST_CONDITIONAL
            flags: 0
            cond: AST_CONDITIONAL
                flags: 0
                cond: AST_VAR
                    flags: 0
                    name: "a"
                true: AST_VAR
                    flags: 0
                    name: "b"
                false: AST_VAR
                    flags: 0
                    name: "c"
            true: AST_VAR
                flags: 0
                name: "d"
            false: AST_VAR
                flags: 0
                name: "e"
    1: AST_RETURN
        expr: AST_CONDITIONAL
            flags: 0
            cond: AST_VAR
                flags: 0
                name: "a"
            true: AST_VAR
                flags: 0
                name: "b"
            false: AST_CONDITIONAL
                flags: PARENTHESIZED_CONDITIONAL (1)
                cond: AST_VAR
                    flags: 0
                    name: "c"
                true: AST_VAR
                    flags: 0
                    name: "d"
                false: AST_VAR
                    flags: 0
                    name: "e"