--TEST--
Multiple final multipliers should generate CompileError, not a fatal error
--SKIPIF--
<?php if (PHP_VERSION_ID < 70300) die('skip Requires PHP 7.3+'); ?>
--FILE--
<?php

$code = <<<'PHP'
<?php class C {
    final final function foo($fff) {}
}
PHP;

try {
    ast\parse_code($code, $version=50);
} catch (CompileError $e) {
    echo $e->getMessage(), "\n";
}
?>
--EXPECT--
Multiple final modifiers are not allowed
