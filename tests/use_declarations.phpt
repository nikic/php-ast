--TEST--
Test parse and dump of use declarations
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
use Foo\Bar as Baz;
use function foo\bar as baz;
use Foo\{Bar, function bar};
use function foo\{bar, baz};
PHP;

echo ast_dump(ast\parse_code($code, $version=15));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_USE
        flags: USE_NORMAL (%d)
        0: AST_USE_ELEM
            flags: 0
            0: "Foo\Bar"
            1: "Baz"
    1: AST_USE
        flags: USE_FUNCTION (%d)
        0: AST_USE_ELEM
            flags: 0
            0: "foo\bar"
            1: "baz"
    2: AST_GROUP_USE
        flags: 0
        0: "Foo"
        1: AST_USE
            flags: 0
            0: AST_USE_ELEM
                flags: USE_NORMAL (%d)
                0: "Bar"
                1: null
            1: AST_USE_ELEM
                flags: USE_FUNCTION (%d)
                0: "bar"
                1: null
    3: AST_GROUP_USE
        flags: USE_FUNCTION (%d)
        0: "foo"
        1: AST_USE
            flags: 0
            0: AST_USE_ELEM
                flags: 0
                0: "bar"
                1: null
            1: AST_USE_ELEM
                flags: 0
                0: "baz"
                1: null
