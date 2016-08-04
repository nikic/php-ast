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

echo ast_dump(ast\parse_code($code, $version=15)), "\n";
echo ast_dump(ast\parse_code($code, $version=20)), "\n";
echo ast_dump(ast\parse_code($code, $version=35)), "\n";

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 15 is deprecated in %s on line %d
AST_STMT_LIST
    0: AST_TRY
        0: AST_STMT_LIST
            0: AST_STMT_LIST
                0: AST_ECHO
                    0: "try"
        1: AST_CATCH_LIST
            0: AST_CATCH
                0: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    0: "Exception"
                1: "e"
                2: AST_STMT_LIST
                    0: AST_STMT_LIST
                        0: AST_ECHO
                            0: "catch 1"
            1: AST_CATCH
                0: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    0: "bar\FooException"
                1: "e2"
                2: AST_STMT_LIST
                    0: AST_STMT_LIST
                        0: AST_ECHO
                            0: "catch 2"
        2: AST_STMT_LIST
            0: AST_STMT_LIST
                0: AST_ECHO
                    0: "finally"
AST_STMT_LIST
    0: AST_TRY
        0: AST_STMT_LIST
            0: AST_ECHO
                0: "try"
        1: AST_CATCH_LIST
            0: AST_CATCH
                0: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    0: "Exception"
                1: AST_VAR
                    0: "e"
                2: AST_STMT_LIST
                    0: AST_ECHO
                        0: "catch 1"
            1: AST_CATCH
                0: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    0: "bar\FooException"
                1: AST_VAR
                    0: "e2"
                2: AST_STMT_LIST
                    0: AST_ECHO
                        0: "catch 2"
        2: AST_STMT_LIST
            0: AST_ECHO
                0: "finally"
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
