--TEST--
try / catch / finally
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip ast extension not loaded";
if (!extension_loaded("tokenizer")) print "skip tokenizer extension not loaded";
?>
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

echo ast_dump(ast\parse_code($code, $version=10)), "\n";
echo ast_dump(ast\parse_code($code, $version=20)), "\n";

?>
--EXPECT--
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
