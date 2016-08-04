--TEST--
Array destructuring
--SKIPIF--
<?php if (PHP_VERSION_ID < 70100) die('skip PHP >= 7.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
list('foo' => $a, 'bar' => $b) = $x;
[$a, $b] = $x;
['foo' => $a, 'bar' => $b] = $x;
[, [$a]] = $x;
PHP;

echo ast_dump(ast\parse_code($code, $version=30));

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
                key: "foo"
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "b"
                key: "bar"
        expr: AST_VAR
            name: "x"
    1: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_SHORT (3)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "a"
                key: null
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "b"
                key: null
        expr: AST_VAR
            name: "x"
    2: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_SHORT (3)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "a"
                key: "foo"
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "b"
                key: "bar"
        expr: AST_VAR
            name: "x"
    3: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_SHORT (3)
            0: null
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_ARRAY
                    flags: ARRAY_SYNTAX_SHORT (3)
                    0: AST_ARRAY_ELEM
                        flags: 0
                        value: AST_VAR
                            name: "a"
                        key: null
                key: null
        expr: AST_VAR
            name: "x"
