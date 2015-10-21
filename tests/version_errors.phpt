--TEST--
Version errors
--FILE--
<?php

try {
    ast\parse_code('<?php ...');
} catch (LogicException $e) {
    echo $e->getMessage(), "\n";
}

try {
    ast\parse_code('<?php ...', $version=100);
} catch (LogicException $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECTF--
No version specified. Current version is 15. All versions (including experimental): {10, 15, 20, %s}
Unknown version 100. Current version is 15. All versions (including experimental): {10, 15, 20, %s}
