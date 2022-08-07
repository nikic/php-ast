--TEST--
Encapsulated variable flags in PHP 8.2
--SKIPIF--
<?php if (PHP_VERSION_ID < 80200) die('skip PHP >= 8.2 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = '<?php "${a}${b->c}{$d}${$e["f"]}${g[\'h\']}{$i{\'j\'}}";';
$node = ast\parse_code($code, $version=85);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_ENCAPS_LIST
        0: AST_VAR
            flags: ENCAPS_VAR_DOLLAR_CURLY (%d)
            name: "a"
        1: AST_VAR
            flags: ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR (%d)
            name: AST_PROP
                expr: AST_CONST
                    name: AST_NAME
                        flags: NAME_NOT_FQ (%d)
                        name: "b"
                prop: "c"
        2: AST_VAR
            flags: 0
            name: "d"
        3: AST_VAR
            flags: ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR (%d)
            name: AST_DIM
                flags: 0
                expr: AST_VAR
                    flags: 0
                    name: "e"
                dim: "f"
        4: AST_DIM
            flags: ENCAPS_VAR_DOLLAR_CURLY (%d)
            expr: AST_VAR
                flags: 0
                name: "g"
            dim: "h"
        5: AST_DIM
            flags: DIM_ALTERNATIVE_SYNTAX (%d)
            expr: AST_VAR
                flags: 0
                name: "i"
            dim: "j"
