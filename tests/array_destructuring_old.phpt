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

echo ast_dump(ast\parse_code($code, $version=30)), "\n";
echo ast_dump(ast\parse_code($code, $version=35));

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 30 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_LIST
            0: AST_VAR
                name: "a"
            1: AST_VAR
                name: "b"
        expr: AST_VAR
            name: "x"
    1: AST_ASSIGN
        var: AST_LIST
            0: null
            1: AST_VAR
                name: "b"
        expr: AST_VAR
            name: "x"
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
                flags: 0
                value: AST_VAR
                    name: "b"
                key: null
        expr: AST_VAR
            name: "x"
    1: AST_ASSIGN
        var: AST_ARRAY
            flags: ARRAY_SYNTAX_LIST (1)
            0: null
            1: AST_ARRAY_ELEM
                flags: 0
                value: AST_VAR
                    name: "b"
                key: null
        expr: AST_VAR
            name: "x"
