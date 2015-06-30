--TEST--
Test ast\get_kind_name()
--FILE--
<?php

var_dump(ast\get_kind_name(ast\AST_VAR));
var_dump(ast\get_kind_name(ast\AST_NAME));
var_dump(ast\get_kind_name(ast\AST_CLOSURE_VAR));

try {
    var_dump(ast\get_kind_name(12345));
} catch (LogicException $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECT--
string(7) "AST_VAR"
string(8) "AST_NAME"
string(15) "AST_CLOSURE_VAR"
Unknown kind 12345
