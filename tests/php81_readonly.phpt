--TEST--
Readonly properties in php 8.1
--SKIPIF--
<?php if (PHP_VERSION_ID < 80100) die('skip PHP >= 8.1 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class X {
    public readonly int $var;
}
PHP;

$node = ast\parse_code($code, $version=60);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
Deprecated: ast\parse_code(): Version 60 is deprecated in %sphp81_readonly.php on line 12
AST_STMT_LIST
    0: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC | MODIFIER_READONLY (%d)
                0: AST_PROP_ELEM
                    name: "var"
                    default: null
                    docComment: null
        __declId: 0
AST_STMT_LIST
    0: AST_CLASS
        name: "X"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_READONLY (%d)
                type: AST_TYPE
                    flags: TYPE_LONG (%d)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "var"
                        default: null
                        docComment: null
                attributes: null
        attributes: null
        type: null
        __declId: 0