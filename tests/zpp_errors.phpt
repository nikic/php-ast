--TEST--
zpp failures throw TypeError
--FILE--
<?php

try { ast\parse_code(); }
catch (TypeError $e) { echo $e->getMessage(), "\n"; }
try { ast\parse_file(); }
catch (TypeError $e) { echo $e->getMessage(), "\n"; }
try { ast\get_kind_name(); }
catch (TypeError $e) { echo $e->getMessage(), "\n"; }
try { ast\kind_uses_flags(); }
catch (TypeError $e) { echo $e->getMessage(), "\n"; }

?>
--EXPECT--
ast\parse_code() expects at least 1 parameter, 0 given
ast\parse_file() expects exactly 1 parameter, 0 given
ast\get_kind_name() expects exactly 1 parameter, 0 given
ast\kind_uses_flags() expects exactly 1 parameter, 0 given
