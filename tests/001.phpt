--TEST--
Dump of dump function
--SKIPIF--
<?php if (!extension_loaded("ast")) print "skip"; ?>
--FILE--
<?php 

require __DIR__ . '/../util.php';

echo ast_dump(ast\parse_code(file_get_contents(__DIR__ . '/../util.php')));
--EXPECT--
AST_STMT_LIST @ 1
    0: AST_FUNC_DECL @ 4-28
        flags: 0
        docComment: /** Dumps abstract syntax tree */
        0: AST_PARAM_LIST @ 4
            0: AST_PARAM @ 4
                flags: 0
                0: null
                1: "ast"
                2: null
        1: null
        2: AST_STMT_LIST @ 4
            0: AST_IF @ 21
                0: AST_IF_ELEM @ 5
                    0: AST_INSTANCEOF @ 5
                        0: AST_VAR @ 5
                            0: "ast"
                        1: AST_NAME @ 5
                            flags: 1
                            0: "ast\Node"
                    1: AST_STMT_LIST @ 5
                        0: AST_ASSIGN @ 6
                            0: AST_VAR @ 6
                                0: "result"
                            1: AST_CALL @ 6
                                0: AST_NAME @ 6
                                    flags: 1
                                    0: "ast\get_kind_name"
                                1: AST_ARG_LIST @ 6
                                    0: AST_PROP @ 6
                                        0: AST_VAR @ 6
                                            0: "ast"
                                        1: "kind"
                        1: AST_ASSIGN_OP @ 7
                            flags: 30
                            0: AST_VAR @ 7
                                0: "result"
                            1: AST_ENCAPS_LIST @ 7
                                0: " @ "
                                1: AST_PROP @ 7
                                    0: AST_VAR @ 7
                                        0: "ast"
                                    1: "lineno"
                        2: AST_IF @ 10
                            0: AST_IF_ELEM @ 8
                                0: AST_ISSET @ 8
                                    0: AST_PROP @ 8
                                        0: AST_VAR @ 8
                                            0: "ast"
                                        1: "endLineno"
                                1: AST_STMT_LIST @ 8
                                    0: AST_ASSIGN_OP @ 9
                                        flags: 30
                                        0: AST_VAR @ 9
                                            0: "result"
                                        1: AST_ENCAPS_LIST @ 9
                                            0: "-"
                                            1: AST_PROP @ 9
                                                0: AST_VAR @ 9
                                                    0: "ast"
                                                1: "endLineno"
                        3: AST_IF @ 13
                            0: AST_IF_ELEM @ 11
                                0: AST_CALL @ 11
                                    0: AST_NAME @ 11
                                        flags: 1
                                        0: "ast\kind_uses_flags"
                                    1: AST_ARG_LIST @ 11
                                        0: AST_PROP @ 11
                                            0: AST_VAR @ 11
                                                0: "ast"
                                            1: "kind"
                                1: AST_STMT_LIST @ 11
                                    0: AST_ASSIGN_OP @ 12
                                        flags: 30
                                        0: AST_VAR @ 12
                                            0: "result"
                                        1: AST_ENCAPS_LIST @ 12
                                            0: "
                                                flags: "
                                            1: AST_PROP @ 12
                                                0: AST_VAR @ 12
                                                    0: "ast"
                                                1: "flags"
                        4: AST_IF @ 16
                            0: AST_IF_ELEM @ 14
                                0: AST_ISSET @ 14
                                    0: AST_PROP @ 14
                                        0: AST_VAR @ 14
                                            0: "ast"
                                        1: "docComment"
                                1: AST_STMT_LIST @ 14
                                    0: AST_ASSIGN_OP @ 15
                                        flags: 30
                                        0: AST_VAR @ 15
                                            0: "result"
                                        1: AST_ENCAPS_LIST @ 15
                                            0: "
                                                docComment: "
                                            1: AST_PROP @ 15
                                                0: AST_VAR @ 15
                                                    0: "ast"
                                                1: "docComment"
                        5: AST_FOREACH @ 17
                            0: AST_PROP @ 17
                                0: AST_VAR @ 17
                                    0: "ast"
                                1: "children"
                            1: AST_VAR @ 17
                                0: "child"
                            2: AST_VAR @ 17
                                0: "i"
                            3: AST_STMT_LIST @ 17
                                0: AST_ASSIGN_OP @ 18
                                    flags: 30
                                    0: AST_VAR @ 18
                                        0: "result"
                                    1: AST_BINARY_OP @ 18
                                        flags: 8
                                        0: AST_ENCAPS_LIST @ 18
                                            0: "
                                                "
                                            1: AST_VAR @ 18
                                                0: "i"
                                            2: ": "
                                        1: AST_CALL @ 18
                                            0: AST_NAME @ 18
                                                flags: 1
                                                0: "str_replace"
                                            1: AST_ARG_LIST @ 18
                                                0: "
                                                "
                                                1: "
                                                    "
                                                2: AST_CALL @ 18
                                                    0: AST_NAME @ 18
                                                        flags: 1
                                                        0: "ast_dump"
                                                    1: AST_ARG_LIST @ 18
                                                        0: AST_VAR @ 18
                                                            0: "child"
                        6: AST_RETURN @ 20
                            0: AST_VAR @ 20
                                0: "result"
                1: AST_IF_ELEM @ 23
                    0: null
                    1: AST_IF @ 23
                        0: AST_IF_ELEM @ 21
                            0: AST_BINARY_OP @ 21
                                flags: 15
                                0: AST_VAR @ 21
                                    0: "ast"
                                1: AST_CONST @ 21
                                    0: AST_NAME @ 21
                                        flags: 1
                                        0: "null"
                            1: AST_STMT_LIST @ 21
                                0: AST_RETURN @ 22
                                    0: "null"
                        1: AST_IF_ELEM @ 25
                            0: null
                            1: AST_IF @ 25
                                0: AST_IF_ELEM @ 23
                                    0: AST_CALL @ 23
                                        0: AST_NAME @ 23
                                            flags: 1
                                            0: "is_string"
                                        1: AST_ARG_LIST @ 23
                                            0: AST_VAR @ 23
                                                0: "ast"
                                    1: AST_STMT_LIST @ 23
                                        0: AST_RETURN @ 24
                                            0: AST_ENCAPS_LIST @ 24
                                                0: """
                                                1: AST_VAR @ 24
                                                    0: "ast"
                                                2: """
                                1: AST_IF_ELEM @ 25
                                    0: null
                                    1: AST_STMT_LIST @ 25
                                        0: AST_RETURN @ 26
                                            0: AST_CAST @ 26
                                                flags: 6
                                                0: AST_VAR @ 26
                                                    0: "ast"
