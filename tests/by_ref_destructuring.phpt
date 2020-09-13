--TEST--
By-reference array destructuring (PHP 7.3)
--SKIPIF--
<?php if (PHP_VERSION_ID < 70300) die('skip PHP >= 7.3 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
list($a, &$b) = $c;
[$a, &$b] = $c;
$c = [$a, &$b];
PHP;
echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_LIST (1)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "a"
                key: null
            1: AST_ARRAY_ELEM
                flags: ARRAY_ELEM_REF (1)
                value: AST_VAR
                    name: "b"
                key: null
        expr: AST_VAR
            name: "c"
    1: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_SHORT (3)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "a"
                key: null
            1: AST_ARRAY_ELEM
                flags: ARRAY_ELEM_REF (1)
                value: AST_VAR
                    name: "b"
                key: null
        expr: AST_VAR
            name: "c"
    2: AST_ASSIGN
        var: AST_VAR
            name: "c"
        expr: AST_ARRAY
            flags: ARRAY_SYNTAX_SHORT (3)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "a"
                key: null
            1: AST_ARRAY_ELEM
                flags: ARRAY_ELEM_REF (1)
                value: AST_VAR
                    name: "b"
                key: null
