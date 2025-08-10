--TEST--
ast\get_supported_versions() function
--FILE--
<?php

var_dump(ast\get_supported_versions());
var_dump(ast\get_supported_versions(true));

?>
--EXPECT--
array(9) {
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
  [6]=>
  int(100)
  [7]=>
  int(110)
  [8]=>
  int(120)
}
array(7) {
  [0]=>
  int(70)
  [1]=>
  int(80)
  [2]=>
  int(85)
  [3]=>
  int(90)
  [4]=>
  int(100)
  [5]=>
  int(110)
  [6]=>
  int(120)
}
