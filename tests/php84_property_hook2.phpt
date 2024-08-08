--TEST--
Property hooks in php 8.4 bodies
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >=8.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class A {
    public string $normal;
    public string $name {
        set(string $newName) {
            $this->name = $newName;
        }
    }
}
PHP;

$node = ast\parse_code($code, $version=90);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "normal"
                        default: null
                        docComment: null
                attributes: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "name"
                        default: null
                        docComment: null
                attributes: null
        attributes: null
        type: null
        __declId: 0
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "normal"
                        default: null
                        docComment: null
                        hooks: null
                attributes: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (1)
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "name"
                        default: null
                        docComment: null
                        hooks: AST_STMT_LIST
                            0: AST_PROPERTY_HOOK
                                name: "set"
                                docComment: null
                                params: AST_PARAM_LIST
                                    0: AST_PARAM
                                        type: AST_TYPE
                                            flags: TYPE_STRING (6)
                                        name: "newName"
                                        default: null
                                        attributes: null
                                        docComment: null
                                        hooks: null
                                uses: null
                                stmts: AST_STMT_LIST
                                    0: AST_ASSIGN
                                        var: AST_PROP
                                            expr: AST_VAR
                                                name: "this"
                                            prop: "name"
                                        expr: AST_VAR
                                            name: "newName"
                                returnType: null
                                attributes: null
                                __declId: 0
                attributes: null
        attributes: null
        type: null
        __declId: 1
