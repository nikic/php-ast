--TEST--
Property hooks in php 8.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >=8.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
abstract class A {
    abstract public $prop { get; set; }
}
PHP;

$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: CLASS_ABSTRACT (%d)
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_ABSTRACT (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "prop"
                        default: null
                        docComment: null
                        hooks: AST_STMT_LIST
                            0: AST_PROPERTY_HOOK
                                name: "get"
                                docComment: null
                                params: null
                                uses: null
                                stmts: null
                                returnType: null
                                attributes: null
                                __declId: 0
                            1: AST_PROPERTY_HOOK
                                name: "set"
                                docComment: null
                                params: null
                                uses: null
                                stmts: null
                                returnType: null
                                attributes: null
                                __declId: 1
                attributes: null
        attributes: null
        type: null
        __declId: 2
