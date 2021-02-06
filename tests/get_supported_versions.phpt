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
  int(50)
  [1]=>
  int(60)
  [2]=>
  int(70)
  [3]=>
  int(80)
  [4]=>
  int(85)
}
array(5) {
  [0]=>
  int(50)
  [1]=>
  int(60)
  [2]=>
  int(70)
  [3]=>
  int(80)
  [4]=>
  int(85)
}