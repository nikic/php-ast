--TEST--
Convert unary ops AST_(SILENCE|UNARY_(PLUS|MINUS)) to flags of ZEND_AST_UNARY_OP
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
@$a;
+1;
-1;
PHP;

echo ast_dump(ast\parse_code($code, $version=50)), "\n";

?>
--EXPECT--
AST_STMT_LIST
    0: AST_UNARY_OP
        flags: UNARY_SILENCE (260)
        expr: AST_VAR
            name: "a"
    1: AST_UNARY_OP
        flags: UNARY_PLUS (261)
        expr: 1
    2: AST_UNARY_OP
        flags: UNARY_MINUS (262)
        expr: 1
