--TEST--
Multi catch
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
try {
} catch (A|B $b) {
}
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECT--
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
        finally: null
