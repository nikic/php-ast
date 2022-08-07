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
echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "fn"
        expr: AST_CLOSURE
            flags: MODIFIER_STATIC | FUNC_RETURNS_REF (%d)
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
                0: AST_PARAM
                    flags: 0
                    type: null
                    name: "a"
                    default: null
                1: AST_PARAM
                    flags: PARAM_REF (%d)
                    type: null
                    name: "b"
                    default: null
            uses: AST_CLOSURE_USES
                0: AST_CLOSURE_VAR
                    flags: 0
                    name: "c"
                1: AST_CLOSURE_VAR
                    flags: CLOSURE_USE_REF (1)
                    name: "d"
            stmts: AST_STMT_LIST
            returnType: null
            __declId: 0
