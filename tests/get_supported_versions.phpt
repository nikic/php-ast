--TEST--
ast\get_supported_versions() function
--FILE--
<?php

var_dump(ast\get_supported_versions());
var_dump(ast\get_supported_versions(true));

?>
--EXPECT--
array(4) {
  [0]=>
  int(40)
  [1]=>
  int(45)
  [2]=>
  int(50)
  [3]=>
  int(60)
}
array(2) {
  [0]=>
  int(50)
  [1]=>
  int(60)
}
