--TEST--
ast\get_supported_versions() function
--FILE--
<?php

var_dump(ast\get_supported_versions());
var_dump(ast\get_supported_versions(true));

?>
--EXPECT--
array(5) {
  [0]=>
  int(35)
  [1]=>
  int(40)
  [2]=>
  int(45)
  [3]=>
  int(50)
  [4]=>
  int(60)
}
array(2) {
  [0]=>
  int(50)
  [1]=>
  int(60)
}
