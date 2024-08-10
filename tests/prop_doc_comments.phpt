--TEST--
Doc comments on properties
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class A {
    /** docComment $a */
    public $a;

    public
        /** docComment $b */
        $b,
        /** docComment $c */
        $c
    ;
}
PHP;

echo ast_dump(ast\parse_code($code, $version=50)), "\n";
echo ast_dump(ast\parse_code($code, $version=80)), "\n";
echo ast_dump(ast\parse_code($code, $version=110)), "\n";

?>
--EXPECTF--
Deprecated: ast\parse_code(): Version 50 is deprecated in %s.php on line 20
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "a"
                    default: null
                    docComment: "/** docComment $a */"
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "b"
                    default: null
                    docComment: "/** docComment $b */"
                1: AST_PROP_ELEM
                    name: "c"
                    default: null
                    docComment: "/** docComment $c */"
        __declId: 0
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "a"
                        default: null
                        docComment: "/** docComment $a */"
                attributes: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "b"
                        default: null
                        docComment: "/** docComment $b */"
                    1: AST_PROP_ELEM
                        name: "c"
                        default: null
                        docComment: "/** docComment $c */"
                attributes: null
        attributes: null
        __declId: 0
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "a"
                        default: null
                        docComment: "/** docComment $a */"
                        hooks: null
                attributes: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "b"
                        default: null
                        docComment: "/** docComment $b */"
                        hooks: null
                    1: AST_PROP_ELEM
                        name: "c"
                        default: null
                        docComment: "/** docComment $c */"
                        hooks: null
                attributes: null
        attributes: null
        type: null
        __declId: 0
