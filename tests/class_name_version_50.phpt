--TEST--
Class properties in AST version 50
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace Foo;
echo Bar::class;
echo namespace\Bat::class;
echo \Baz::class;
PHP;

$node = ast\parse_code($code, $version=50);
echo ast_dump($node), "\n";
?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 50 is deprecated in %s.php on line 13
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "Foo"
        stmts: null
    1: AST_ECHO
        expr: AST_CLASS_CONST
            class: AST_NAME
                flags: NAME_NOT_FQ (1)
                name: "Bar"
            const: "class"
    2: AST_ECHO
        expr: AST_CLASS_CONST
            class: AST_NAME
                flags: NAME_RELATIVE (2)
                name: "Bat"
            const: "class"
    3: AST_ECHO
        expr: AST_CLASS_CONST
            class: AST_NAME
                flags: NAME_FQ (0)
                name: "Baz"
            const: "class"
