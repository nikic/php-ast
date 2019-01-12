--TEST--
Type hints in PHP 7.4
--SKIPIF--
<?php if (PHP_VERSION_ID < 70400) die('skip PHP >= 7.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
namespace Foo;
class test {
	public int $i = 2, $j;
	public static ?string $s;
	private ?iterable $it;
	protected Row $row;
	var \stdClass $o;
	private static $normal = null;
}
PHP;

$node = ast\parse_code($code, $version=60);
echo "The type property should not be set in version 60\n";
echo ast_dump($node), "\n";
echo "But the type property should be set in version 70\n";
$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
?>
--EXPECTF--
The type property should not be set in version 60
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "Foo"
        stmts: null
    1: AST_CLASS
        flags: 0
        name: "test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "i"
                    default: 2
                    docComment: null
                1: AST_PROP_ELEM
                    name: "j"
                    default: null
                    docComment: null
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC | MODIFIER_STATIC (%d)
                0: AST_PROP_ELEM
                    name: "s"
                    default: null
                    docComment: null
            2: AST_PROP_DECL
                flags: MODIFIER_PRIVATE (%d)
                0: AST_PROP_ELEM
                    name: "it"
                    default: null
                    docComment: null
            3: AST_PROP_DECL
                flags: MODIFIER_PROTECTED (%d)
                0: AST_PROP_ELEM
                    name: "row"
                    default: null
                    docComment: null
            4: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "o"
                    default: null
                    docComment: null
            5: AST_PROP_DECL
                flags: MODIFIER_PRIVATE | MODIFIER_STATIC (%d)
                0: AST_PROP_ELEM
                    name: "normal"
                    default: AST_CONST
                        name: AST_NAME
                            flags: NAME_NOT_FQ (1)
                            name: "null"
                    docComment: null
        __declId: 0
But the type property should be set in version 70
AST_STMT_LIST
    0: AST_NAMESPACE
        name: "Foo"
        stmts: null
    1: AST_CLASS
        flags: 0
        name: "test"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "i"
                        default: 2
                        docComment: null
                    1: AST_PROP_ELEM
                        name: "j"
                        default: null
                        docComment: null
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC | MODIFIER_STATIC (%d)
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_STRING (6)
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "s"
                        default: null
                        docComment: null
            2: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE (%d)
                type: AST_NULLABLE_TYPE
                    type: AST_TYPE
                        flags: TYPE_ITERABLE (18)
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "it"
                        default: null
                        docComment: null
            3: AST_PROP_GROUP
                flags: MODIFIER_PROTECTED (%d)
                type: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "Row"
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "row"
                        default: null
                        docComment: null
            4: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: AST_NAME
                    flags: NAME_FQ (0)
                    name: "stdClass"
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "o"
                        default: null
                        docComment: null
            5: AST_PROP_GROUP
                flags: MODIFIER_PRIVATE | MODIFIER_STATIC (%d)
                type: null
                props: AST_PROP_DECL
                    flags: 0
                    0: AST_PROP_ELEM
                        name: "normal"
                        default: AST_CONST
                            name: AST_NAME
                                flags: NAME_NOT_FQ (1)
                                name: "null"
                        docComment: null
        __declId: 0
