--TEST--
'$x{"offset"}' flag in PHP 8.4 is a Parse error
--SKIPIF--
<?php if (PHP_VERSION_ID < 80400) die('skip PHP >= 8.4 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
var_export($x{'offset'});
PHP;

try {
    $node = ast\parse_code($code, $version=70);
    echo ast_dump($node), "\n";
} catch (ParseError $e) {
    echo "Caught: ", $e->getMessage(), "\n";
}
?>
--EXPECT--
Caught: syntax error, unexpected token "{", expecting ")"
