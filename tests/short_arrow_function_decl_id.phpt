--TEST--
Nested arrow functions in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
$cb = fn() => fn() => $undef;
PHP;

$node = ast\parse_code($code, $version=85);
echo ast_dump($node) . "\n";
?>
--EXPECT--
AST_STMT_LIST
    0: AST_ASSIGN
        var: AST_VAR
            name: "cb"
        expr: AST_ARROW_FUNC
            name: "{closure}"
            docComment: null
            params: AST_PARAM_LIST
            stmts: AST_RETURN
                expr: AST_ARROW_FUNC
                    name: "{closure}"
                    docComment: null
                    params: AST_PARAM_LIST
                    stmts: AST_RETURN
                        expr: AST_VAR
                            name: "undef"
                    returnType: null
                    attributes: null
                    __declId: 0
            returnType: null
            attributes: null
            __declId: 1