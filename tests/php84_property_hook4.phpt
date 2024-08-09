--TEST--
Property hooks in php 8.4 constructor property promotion
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >=8.4 only'); ?>
--FILE--
<?php
require __DIR__ . '/../util.php';
$code = <<<'PHP'
<?php
class User
{
    public function __construct(
        public string $username { set => strtolower($value); }
    ) {}
}
PHP;
$node = ast\parse_code($code, $version=110);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        name: "User"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "__construct"
                docComment: null
                params: AST_PARAM_LIST
                    0: AST_PARAM
                        flags: PARAM_MODIFIER_PUBLIC (%d)
                        type: AST_TYPE
                            flags: TYPE_STRING (%d)
                        name: "username"
                        default: null
                        attributes: null
                        docComment: null
                        hooks: AST_STMT_LIST
                            0: AST_PROPERTY_HOOK
                                name: "set"
                                docComment: null
                                params: null
                                stmts: AST_PROPERTY_HOOK_SHORT_BODY
                                    expr: AST_CALL
                                        expr: AST_NAME
                                            flags: NAME_NOT_FQ (%d)
                                            name: "strtolower"
                                        args: AST_ARG_LIST
                                            0: AST_VAR
                                                name: "value"
                                attributes: null
                                __declId: 0
                stmts: AST_STMT_LIST
                returnType: null
                attributes: null
                __declId: 1
        attributes: null
        type: null
        __declId: 2
