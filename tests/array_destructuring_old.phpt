--TEST--
Array destructuring (using unkeyed list())
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
list($a, $b) = $x;
list(, $b) = $x;
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_LIST (%d)
            0: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    flags: 0
                    name: "a"
                key: null
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    flags: 0
                    name: "b"
                key: null
        expr: AST_VAR
            flags: 0
            name: "x"
    1: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_LIST (%d)
            0: null
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    flags: 0
                    name: "b"
                key: null
        expr: AST_VAR
            flags: 0
            name: "x"