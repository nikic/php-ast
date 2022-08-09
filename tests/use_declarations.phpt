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

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_USE
        flags: USE_NORMAL (%d)
        0: AST_USE_ELEM
            flags: 0
            name: "Foo\Bar"
            alias: "Baz"
    1: AST_USE
        flags: USE_FUNCTION (%d)
        0: AST_USE_ELEM
            flags: 0
            name: "foo\bar"
            alias: "baz"
    2: AST_GROUP_USE
        flags: 0
        prefix: "Foo"
        uses: AST_USE
            flags: 0
            0: AST_USE_ELEM
                flags: USE_NORMAL (%d)
                name: "Bar"
                alias: null
            1: AST_USE_ELEM
                flags: USE_FUNCTION (%d)
                name: "bar"
                alias: null
    3: AST_GROUP_USE
        flags: USE_FUNCTION (%d)
        prefix: "foo"
        uses: AST_USE
            flags: 0
            0: AST_USE_ELEM
                flags: 0
                name: "bar"
                alias: null
            1: AST_USE_ELEM
                flags: 0
                name: "baz"
                alias: null