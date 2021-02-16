--TEST--
Magic constants
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
__LINE__;
__FILE__;
__DIR__;
__NAMESPACE__;
__FUNCTION__;
__METHOD__;
__CLASS__;
__TRAIT__;
PHP;

echo ast_dump(ast\parse_code($code, $version=70));

?>
--EXPECTF--
AST_STMT_LIST
    0: AST_MAGIC_CONST
        flags: MAGIC_LINE (%d)
    1: AST_MAGIC_CONST
        flags: MAGIC_FILE (%d)
    2: AST_MAGIC_CONST
        flags: MAGIC_DIR (%d)
    3: AST_MAGIC_CONST
        flags: MAGIC_NAMESPACE (%d)
    4: AST_MAGIC_CONST
        flags: MAGIC_FUNCTION (%d)
    5: AST_MAGIC_CONST
        flags: MAGIC_METHOD (%d)
    6: AST_MAGIC_CONST
        flags: MAGIC_CLASS (%d)
    7: AST_MAGIC_CONST
        flags: MAGIC_TRAIT (%d)
