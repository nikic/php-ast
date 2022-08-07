--TEST--
ast\parse_file() on file that does not exist
--FILE--
<?php

try {
    ast\parse_file(__DIR__ . '/non_existing_file.php', $version=70);
} catch (RuntimeException $e) {
    echo $e, "\n";
}

?>
--EXPECTF--
RuntimeException: ast\parse_file(%stests/non_existing_file.php): %sailed to open stream: No such file or directory in %s:%d
Stack trace:
#0 %s(%d): ast\parse_file('%s', %d)
#1 {main}
