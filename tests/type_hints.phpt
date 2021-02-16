--TEST--
Type hints
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
function test(
    A $a, array $b, callable $c, INT $d, Float $e, string $f, bool $g, iterable $h
) : void {
}
PHP;

echo ast_dump(ast\parse_code($code, $version=70)), "\n";

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_FUNC_DECL
        flags: 0
        name: "test"
        docComment: null
        params: AST_PARAM_LIST
            0: AST_PARAM
                flags: 0
                type: AST_NAME
                    flags: NAME_NOT_FQ (1)
                    name: "A"
                name: "a"
                default: null
            1: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_ARRAY (7)
                name: "b"
                default: null
            2: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_CALLABLE (%d)
                name: "c"
                default: null
            3: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_LONG (4)
                name: "d"
                default: null
            4: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_DOUBLE (5)
                name: "e"
                default: null
            5: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_STRING (6)
                name: "f"
                default: null
            6: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_BOOL (%d)
                name: "g"
                default: null
            7: AST_PARAM
                flags: 0
                type: AST_TYPE
                    flags: TYPE_ITERABLE (%d)
                name: "h"
                default: null
        stmts: AST_STMT_LIST
        returnType: AST_TYPE
            flags: TYPE_VOID (%d)
        __declId: 0
