--TEST--
ast\get_supported_versions() function
--FILE--
<?php

var_dump(ast\get_supported_versions());
var_dump(ast\get_supported_versions(true));

?>
--EXPECT--
array(6) {
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
  [5]=>
  int(90)
}
array(4) {
  [0]=>
  int(70)
  [1]=>
  int(80)
  [2]=>
  int(85)
  [3]=>
  int(90)
}