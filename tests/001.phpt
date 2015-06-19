--TEST--
Dump of dump function
--SKIPIF--
<?php if (!extension_loaded("ast")) print "skip"; ?>
--FILE--
<?php 

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php

/** Test function */
function test(Type $arg = XYZ) {
    if ($arg instanceof Foo\Bar) {
        return test($arg->foo);
    } else {
        return $arg->bar;
    }
}
PHP;

echo ast_dump(ast\parse_code($code));
--EXPECT--
AST_STMT_LIST @ 1
    0: AST_FUNC_DECL @ 4-10
        flags: 0
        name: test
        docComment: /** Test function */
        0: AST_PARAM_LIST @ 4
            0: AST_PARAM @ 4
                flags: 0
                0: AST_NAME @ 4
                    flags: 1
                    0: "Type"
                1: "arg"
                2: AST_CONST @ 4
                    0: AST_NAME @ 4
                        flags: 1
                        0: "XYZ"
        1: null
        2: AST_STMT_LIST @ 4
            0: AST_IF @ 7
                0: AST_IF_ELEM @ 5
                    0: AST_INSTANCEOF @ 5
                        0: AST_VAR @ 5
                            0: "arg"
                        1: AST_NAME @ 5
                            flags: 1
                            0: "Foo\Bar"
                    1: AST_STMT_LIST @ 5
                        0: AST_RETURN @ 6
                            0: AST_CALL @ 6
                                0: AST_NAME @ 6
                                    flags: 1
                                    0: "test"
                                1: AST_ARG_LIST @ 6
                                    0: AST_PROP @ 6
                                        0: AST_VAR @ 6
                                            0: "arg"
                                        1: "foo"
                1: AST_IF_ELEM @ 7
                    0: null
                    1: AST_STMT_LIST @ 7
                        0: AST_RETURN @ 8
                            0: AST_PROP @ 8
                                0: AST_VAR @ 8
                                    0: "arg"
                                1: "bar"
