--TEST--
Node::parseCode() on valid code with subclass
--FILE--
<?php

require __DIR__ . '/../util.php';

class MyNode extends \ast\Node {
}

$code = <<<'PHP'
<?php

return 123;
PHP;

echo get_class(MyNode::parseCode($code, $version=50)), "\n";

?>
--EXPECT--
MyNode
