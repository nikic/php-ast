--TEST--
Multi catch
--SKIPIF--
<?php if (PHP_VERSION_ID < 70100) die('skip PHP >= 7.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
try {
} catch (A|B $b) {
}
PHP;

echo ast_dump(ast\parse_code($code, $version=30));
echo ast_dump(ast\parse_code($code, $version=35));

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 30 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_TRY
        try: AST_STMT_LIST
        catches: AST_CATCH_LIST
            0: AST_CATCH
                class: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "A"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "B"
                var: AST_VAR
                    name: "b"
                stmts: AST_STMT_LIST
        finally: nullAST_STMT_LIST
    0: AST_TRY
        try: AST_STMT_LIST
        catches: AST_CATCH_LIST
            0: AST_CATCH
                class: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "A"
                    1: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "B"
                var: AST_VAR
                    name: "b"
                stmts: AST_STMT_LIST
        finally: null
