--TEST--
Flag on generator functions in PHP 7.1
--SKIPIF--
<?php
if (PHP_VERSION_ID < 70100) die('skip requires PHP 7.1');
?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function gen() {
    yield;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: FUNC_GENERATOR (%d)
        name: "gen"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
            0: AST_YIELD
                value: null
                key: null
        returnType: null
        __declId: 0
