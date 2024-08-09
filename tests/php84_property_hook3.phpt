--TEST--
Property hooks in php 8.4 bodies flags
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >=8.4 only'); ?>
--FILE--
<?php
require __DIR__ . '/../util.php';
$code = <<<'PHP'
<?php
class A {
    public Generator $values {
        #[MyAttribute]
        get {
            yield 123;
        }
    }
}
PHP;
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
                flags: MODIFIER_PUBLIC (%d)
                type: AST_NAME
                    flags: NAME_NOT_FQ (%d)
                    name: "Generator"
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "values"
                        default: null
                        docComment: null
                        hooks: AST_STMT_LIST
                            0: AST_PROPERTY_HOOK
                                flags: FUNC_GENERATOR (%d)
                                name: "get"
                                docComment: null
                                params: null
                                stmts: AST_STMT_LIST
                                    0: AST_YIELD
                                        value: 123
                                        key: null
                                attributes: AST_ATTRIBUTE_LIST
                                    0: AST_ATTRIBUTE_GROUP
                                        0: AST_ATTRIBUTE
                                            class: AST_NAME
                                                flags: NAME_NOT_FQ (%d)
                                                name: "MyAttribute"
                                            args: null
                                __declId: 0
                attributes: null
        attributes: null
        type: null
        __declId: 1
