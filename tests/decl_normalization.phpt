--TEST--
The class ast\Node\Decl no longer exists
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
/** A */
class A {
    /** B */
    const B = 0;

    /** c */
    public $c;

    /** d */
    function d() {}
}
/** E */
const E = 0;
/** f */
function f() {}
/** g */
function() {};
PHP;

$ast = ast\parse_code($code, $version=70);
var_dump(array_map('get_class', $ast->children));
echo ast_dump($ast) . "\n";
$ast = ast\parse_code($code, $version=50);
echo ast_dump($ast) . "\n";

?>
--EXPECTF--
array(4) {
  [0]=>
  string(8) "ast\Node"
  [1]=>
  string(8) "ast\Node"
  [2]=>
  string(8) "ast\Node"
  [3]=>
  string(8) "ast\Node"
}
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: "/** A */"
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "B"
                    value: 0
                    docComment: "/** B */"
            1: AST_PROP_GROUP
                flags: MODIFIER_PUBLIC (%d)
                type: null
                props: AST_PROP_DECL
                    0: AST_PROP_ELEM
                        name: "c"
                        default: null
                        docComment: "/** c */"
            2: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "d"
                docComment: "/** d */"
                params: AST_PARAM_LIST
                stmts: AST_STMT_LIST
                returnType: null
                __declId: 0
        __declId: 1
    1: AST_CONST_DECL
        0: AST_CONST_ELEM
            name: "E"
            value: 0
            docComment: "/** E */"
    2: AST_FUNC_DECL
        name: "f"
        docComment: "/** f */"
        params: AST_PARAM_LIST
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 2
    3: AST_CLOSURE
        name: "{closure}"
        docComment: "/** g */"
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 3

Deprecated: ast\parse_code(): Version 50 is deprecated in %sdecl_normalization.php on line 29
AST_STMT_LIST
    0: AST_CLASS
        name: "A"
        docComment: "/** A */"
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    name: "B"
                    value: 0
                    docComment: "/** B */"
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    name: "c"
                    default: null
                    docComment: "/** c */"
            2: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "d"
                docComment: "/** d */"
                params: AST_PARAM_LIST
                uses: null
                stmts: AST_STMT_LIST
                returnType: null
                __declId: 0
        __declId: 1
    1: AST_CONST_DECL
        0: AST_CONST_ELEM
            name: "E"
            value: 0
            docComment: "/** E */"
    2: AST_FUNC_DECL
        name: "f"
        docComment: "/** f */"
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 2
    3: AST_CLOSURE
        name: "{closure}"
        docComment: "/** g */"
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 3