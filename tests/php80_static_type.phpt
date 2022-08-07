--TEST--
Union types in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Xyz {
    public function test() : static {
        return $this;
    }
    public function test2() : static|false|OtherClass {
        return $this;
    }
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "Xyz"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "test"
                docComment: null
                params: AST_PARAM_LIST
                stmts: AST_STMT_LIST
                    0: AST_RETURN
                        expr: AST_VAR
                            flags: 0
                            name: "this"
                returnType: AST_TYPE
                    flags: TYPE_STATIC (%d)
                __declId: 0
            1: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "test2"
                docComment: null
                params: AST_PARAM_LIST
                stmts: AST_STMT_LIST
                    0: AST_RETURN
                        expr: AST_VAR
                            flags: 0
                            name: "this"
                returnType: AST_TYPE_UNION
                    0: AST_TYPE
                        flags: TYPE_STATIC (%d)
                    1: AST_TYPE
                        flags: TYPE_FALSE (%d)
                    2: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "OtherClass"
                __declId: 1
        __declId: 2