--TEST--
ast_dump() test
--SKIPIF--
<?php if (!extension_loaded("ast")) print "skip"; ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php

/** Test function */
function test(Type $arg = XYZ) : Ret {
    if ($arg instanceof Foo\Bar) {
        return test($arg->foo);
    } else {
        return $arg->bar;
    }
}
PHP;

echo ast_dump(ast\parse_code($code, $version=70));
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: "/** Test function */"
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "Type"
                name: "arg"
                default: AST_CONST
                    name: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        name: "XYZ"
        stmts: AST_STMT_LIST
            0: AST_IF
                0: AST_IF_ELEM
                    cond: AST_INSTANCEOF
                        expr: AST_VAR
                            flags: 0
                            name: "arg"
                        class: AST_NAME
                            flags: NAME_NOT_FQ (1)
                            name: "Foo\Bar"
                    stmts: AST_STMT_LIST
                        0: AST_RETURN
                            expr: AST_CALL
                                expr: AST_NAME
                                    flags: NAME_NOT_FQ (1)
                                    name: "test"
                                args: AST_ARG_LIST
                                    0: AST_PROP
                                        expr: AST_VAR
                                            flags: 0
                                            name: "arg"
                                        prop: "foo"
                1: AST_IF_ELEM
                    cond: null
                    stmts: AST_STMT_LIST
                        0: AST_RETURN
                            expr: AST_PROP
                                expr: AST_VAR
                                    flags: 0
                                    name: "arg"
                                prop: "bar"
        returnType: AST_NAME
            flags: NAME_NOT_FQ (1)
            name: "Ret"
        __declId: 0