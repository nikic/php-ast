--TEST--
Asymmetric Visibility in php 8.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >=8.4 only'); ?>
--FILE--
<?php
require __DIR__ . '/../util.php';
$code = <<<'PHP'
<?php
class PublicPropsWithAV
{
    public public(set) int $p1 = 0;
    public protected(set) int $p2 = 0;
    public private(set) int $p3 = 0;
    protected public(set) int $p4 = 0;
    protected protected(set) int $p5 = 0;
    protected private(set) int $p6 = 0;
    private public(set) int $p7 = 0;
    private protected(set) int $pp8 = 0;
    private private(set) int $p9 = 0;
}
PHP;
$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "PublicPropsWithAV"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_PUBLIC_SET (1025)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p1"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_PROTECTED_SET (2049)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p2"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            2: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_PRIVATE_SET (4097)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p3"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            3: AST_PROP_GROUP
                flags: MODIFIER_PROTECTED | MODIFIER_PUBLIC_SET (1026)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p4"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            4: AST_PROP_GROUP
                flags: MODIFIER_PROTECTED | MODIFIER_PROTECTED_SET (2050)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p5"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            5: AST_PROP_GROUP
                flags: MODIFIER_PROTECTED | MODIFIER_PRIVATE_SET (4098)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p6"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            6: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_PUBLIC_SET (1028)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p7"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            7: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_PROTECTED_SET (2052)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "pp8"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
            8: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_PRIVATE_SET (4100)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "p9"
                        default: 0
                        docComment: null
                        hooks: null
                attributes: null
        attributes: null
        type: null
        __declId: 0
