--TEST--
ast_dump() test
--SKIPIF--
<?php
if (!extension_loaded("ast")) print "skip ast extension not loaded";
if (!extension_loaded("tokenizer")) print "skip tokenizer extension not loaded";
?>
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

echo ast_dump(ast\parse_code($code, $version=15));
--EXPECT--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: test
        docComment: /** Test function */
        0: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                0: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    0: "Type"
                1: "arg"
                2: AST_CONST
                    0: AST_NAME
                        flags: NAME_NOT_FQ (1)
                        0: "XYZ"
        1: null
        2: AST_STMT_LIST
            0: AST_IF
                0: AST_IF_ELEM
                    0: AST_INSTANCEOF
                        0: AST_VAR
                            0: "arg"
                        1: AST_NAME
                            flags: NAME_NOT_FQ (1)
                            0: "Foo\Bar"
                    1: AST_STMT_LIST
                        0: AST_RETURN
                            0: AST_CALL
                                0: AST_NAME
                                    flags: NAME_NOT_FQ (1)
                                    0: "test"
                                1: AST_ARG_LIST
                                    0: AST_PROP
                                        0: AST_VAR
                                            0: "arg"
                                        1: "foo"
                1: AST_IF_ELEM
                    0: null
                    1: AST_STMT_LIST
                        0: AST_RETURN
                            0: AST_PROP
                                0: AST_VAR
                                    0: "arg"
                                1: "bar"
        3: AST_NAME
            flags: NAME_NOT_FQ (1)
            0: "Ret"
