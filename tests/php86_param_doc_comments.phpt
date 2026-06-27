--TEST--
Doc comments on function/method parameters (PHP 8.6+)
--SKIPIF--
<?php if (PHP_VERSION_ID < 80600) die('skip PHP >=8.6 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function f(
    /** doc for $a */
    int $a,
    $b,
    /** doc for $c */
    string $c = "x",
) {}

class C {
    public string $p {
        set(
            /** doc for $value */
            string $value
        ) { $this->p = $value; }
    }
}
PHP;

echo ast_dump(ast\parse_code($code, $version=120)), "\n";
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        name: "f"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                name: "a"
                default: null
                attributes: null
                docComment: "/** doc for $a */"
                hooks: null
            1: AST_PARAM
                type: null
                name: "b"
                default: null
                attributes: null
                docComment: null
                hooks: null
            2: AST_PARAM
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                name: "c"
                default: "x"
                attributes: null
                docComment: "/** doc for $c */"
                hooks: null
        stmts: AST_STMT_LIST
        returnType: null
        attributes: null
        __declId: 0
    1: AST_CLASS
        name: "C"
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
                        name: "p"
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
                                        name: "value"
                                        default: null
                                        attributes: null
                                        docComment: "/** doc for $value */"
                                        hooks: null
                                stmts: AST_STMT_LIST
                                    0: AST_ASSIGN
                                        var: AST_PROP
                                            expr: AST_VAR
                                                name: "this"
                                            prop: "p"
                                        expr: AST_VAR
                                            name: "value"
                                attributes: null
                                __declId: 1
                attributes: null
        attributes: null
        type: null
        __declId: 2
