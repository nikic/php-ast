--TEST--
Class properties in AST version 70
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace Foo;
echo Bar::class,
namespace\Bat::class,
Bat::class_,  // this is a regular class constant
\Baz::CLASS;
echo []::class;  // this is a runtime error, not a syntax error
echo 'foo'::class;  // this is valid but rare
echo (new \stdClass())::class;  // this is a runtime error, not a syntax error
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECTF--
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "Foo"
        stmts: null
    1: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "Bar"
    2: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_NAME
                flags: NAME_RELATIVE (%d)
                name: "Bat"
    3: AST_ECHO
        expr: AST_CLASS_CONST
            class: AST_NAME
                flags: NAME_NOT_FQ (%d)
                name: "Bat"
            const: "class_"
    4: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_NAME
                flags: NAME_FQ (%d)
                name: "Baz"
    5: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_ARRAY
                flags: %s
    6: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_NAME
                flags: NAME_FQ (%d)
                name: "foo"
    7: AST_ECHO
        expr: AST_CLASS_NAME
            class: AST_NEW
                class: AST_NAME
                    flags: NAME_FQ (0)
                    name: "stdClass"
                args: AST_ARG_LIST
