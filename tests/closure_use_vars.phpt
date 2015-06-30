--TEST--
Closure uses should parse to CLOSURE_USE_VAR nodes
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$fn = static function &($a, &$b) use ($c, &$d) {
};
PHP;
echo ast_dump(ast\parse_code($code));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN
        0: AST_VAR
            0: "fn"
        1: AST_CLOSURE
            flags: MODIFIER_STATIC | RETURNS_REF (67108865)
            name: {closure}
            0: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    0: null
                    1: "a"
                    2: null
                1: AST_PARAM
                    flags: PARAM_REF (1)
                    0: null
                    1: "b"
                    2: null
            1: AST_CLOSURE_USES
                0: AST_CLOSURE_VAR
                    flags: 0
                    0: "c"
                1: AST_CLOSURE_VAR
                    flags: 1
                    0: "d"
            2: AST_STMT_LIST
            3: null
