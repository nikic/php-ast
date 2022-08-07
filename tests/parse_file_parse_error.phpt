--TEST--
ast\parse_file() on file with parse error
--FILE--
<?php

try {
    ast\parse_file(__DIR__ . '/invalid_file.php', $version=70);
} catch (ParseError $e) {
    echo $e, "\n";
}

?>
--EXPECTF--
ParseError: syntax error, unexpected %s)%s in %stests/invalid_file.php:3
Stack trace:
#0 %s(%d): ast\parse_file('%s', %d)
#1 {main}
