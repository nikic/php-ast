--TEST--
try / catch / finally
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
try {
    echo "try";
} catch (Exception $e) {
    echo "catch 1";
} catch (bar\FooException $e2) {
    echo "catch 2";
} finally {
    echo "finally";
}
PHP;

echo ast_dump(ast\parse_code($code, $version=30)), "\n";
echo ast_dump(ast\parse_code($code, $version=35)), "\n";

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 30 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_TRY
        try: AST_STMT_LIST
            0: AST_ECHO
                expr: "try"
        catches: AST_CATCH_LIST
            0: AST_CATCH
                class: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "Exception"
                var: AST_VAR
                    name: "e"
                stmts: AST_STMT_LIST
                    0: AST_ECHO
                        expr: "catch 1"
            1: AST_CATCH
                class: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "bar\FooException"
                var: AST_VAR
                    name: "e2"
                stmts: AST_STMT_LIST
                    0: AST_ECHO
                        expr: "catch 2"
        finally: AST_STMT_LIST
            0: AST_ECHO
                expr: "finally"
AST_STMT_LIST
    0: AST_TRY
        try: AST_STMT_LIST
            0: AST_ECHO
                expr: "try"
        catches: AST_CATCH_LIST
            0: AST_CATCH
                class: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "Exception"
                var: AST_VAR
                    name: "e"
                stmts: AST_STMT_LIST
                    0: AST_ECHO
                        expr: "catch 1"
            1: AST_CATCH
                class: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "bar\FooException"
                var: AST_VAR
                    name: "e2"
                stmts: AST_STMT_LIST
                    0: AST_ECHO
                        expr: "catch 2"
        finally: AST_STMT_LIST
            0: AST_ECHO
                expr: "finally"
