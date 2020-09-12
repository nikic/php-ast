--TEST--
new ast\Node() throwing
--SKIPIF--
<?php if (!extension_loaded("ast")) print "skip"; ?>
--FILE--
<?php

try {
    new ast\Node('invalid');
} catch (TypeError $e) {
    echo "Caught {$e->getMessage()}\n";
}
--EXPECTF--
Caught %s, string given
