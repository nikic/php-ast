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
    ast\parse_code('<?php ...', $version=10000);
} catch (LogicException $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECTF--
No version specified. Current version is %d. All versions (including experimental): {%d, %s}
Unknown version 10000. Current version is %d. All versions (including experimental): {%d, %s}
