--TEST--
Non-capturing catches in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
try {
	foo();
} catch (Exception) {
	echo "ignoring";
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_TRY
        try: AST_STMT_LIST
            0: AST_CALL
                expr: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "foo"
                args: AST_ARG_LIST
        catches: AST_CATCH_LIST
            0: AST_CATCH
                class: AST_NAME_LIST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "Exception"
                var: null
                stmts: AST_STMT_LIST
                    0: AST_ECHO
                        expr: "ignoring"
        finally: null
