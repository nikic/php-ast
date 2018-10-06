--TEST--
As of version 50 Decl is no more
--SKIPIF--
<?php
// This test also tests for doc comments on constants, which are only available as of PHP 7.1
if (PHP_VERSION_ID < 70100) die('skip PHP 7.1 required');
?>
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

$ast = ast\parse_code($code, $version=45);
var_dump(array_map('get_class', $ast->children));
echo ast_dump($ast) . "\n";

$ast = ast\parse_code($code, $version=50);
var_dump(array_map('get_class', $ast->children));
echo ast_dump($ast) . "\n";

?>
--EXPECTF--
array(4) {
  [0]=>
  string(13) "ast\Node\Decl"
  [1]=>
  string(8) "ast\Node"
  [2]=>
  string(13) "ast\Node\Decl"
  [3]=>
  string(13) "ast\Node\Decl"
}
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: A
        docComment: /** A */
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_CLASS_CONST_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_CONST_ELEM
                    docComment: /** B */
                    name: "B"
                    value: 0
            1: AST_PROP_DECL
                flags: MODIFIER_PUBLIC (%d)
                0: AST_PROP_ELEM
                    docComment: /** c */
                    name: "c"
                    default: null
            2: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: d
                docComment: /** d */
                params: AST_PARAM_LIST
                uses: null
                stmts: AST_STMT_LIST
                returnType: null
    1: AST_CONST_DECL
        0: AST_CONST_ELEM
            docComment: /** E */
            name: "E"
            value: 0
    2: AST_FUNC_DECL
        flags: 0
        name: f
        docComment: /** f */
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
    3: AST_CLOSURE
        flags: 0
        name: {closure}
        docComment: /** g */
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
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
        flags: 0
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
        flags: 0
        name: "f"
        docComment: "/** f */"
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 2
    3: AST_CLOSURE
        flags: 0
        name: "{closure}"
        docComment: "/** g */"
        params: AST_PARAM_LIST
        uses: null
        stmts: AST_STMT_LIST
        returnType: null
        __declId: 3
