--TEST--
Named child nodes
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php

$fn = function() use(&$var) {
    $var += func();
};
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "fn"
        expr: AST_CLOSURE
            flags: 0
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
            uses: AST_CLOSURE_USES
                0: AST_CLOSURE_VAR
                    flags: CLOSURE_USE_REF (1)
                    name: "var"
            stmts: AST_STMT_LIST
                0: AST_ASSIGN_OP
                    flags: BINARY_ADD (1)
                    var: AST_VAR
                        name: "var"
                    expr: AST_CALL
                        expr: AST_NAME
                            flags: NAME_NOT_FQ (1)
                            name: "func"
                        args: AST_ARG_LIST
            returnType: null
            __declId: 0
