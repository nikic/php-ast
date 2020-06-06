--TEST--
Mixed types in PHP 8.0
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Xyz {
    public function test(mixed $x) : mixed {
        return $this;
    }
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
$node = ast\parse_code($code, $version=80);
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
                    0: AST_PARAM
                        flags: 0
                        type: AST_NAME
                            flags: NAME_NOT_FQ (%d)
                            name: "mixed"
                        name: "x"
                        default: null
                stmts: AST_STMT_LIST
                    0: AST_RETURN
                        expr: AST_VAR
                            name: "this"
                returnType: AST_NAME
                    flags: NAME_NOT_FQ (%d)
                    name: "mixed"
                __declId: 0
        __declId: 1
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
                    0: AST_PARAM
                        flags: 0
                        type: AST_TYPE
                            flags: TYPE_MIXED (%d)
                        name: "x"
                        default: null
                        attributes: null
                        docComment: null
                stmts: AST_STMT_LIST
                    0: AST_RETURN
                        expr: AST_VAR
                            name: "this"
                returnType: AST_TYPE
                    flags: TYPE_MIXED (%d)
                attributes: null
                __declId: 0
        attributes: null
        __declId: 1