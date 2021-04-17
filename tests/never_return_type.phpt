--TEST--
'never' return type parsing
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function up(): never {
}
PHP;

$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "up"
        docComment: null
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: AST_TYPE
            flags: TYPE_NEVER (%d)
        attributes: null
        __declId: 0
