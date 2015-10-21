--TEST--
ast\parse_code() throwing a ParseError
--FILE--
<?php

$code = '<?php &$(")$/)!"';

try {
    ast\parse_code($code, 15);
} catch (ParseError $e) {
    echo $e, "\n";
}
try {
    ast\parse_code($code, 15, 'file.php');
} catch (ParseError $e) {
    echo $e, "\n";
}

?>
--EXPECTF--
ParseError: syntax error, unexpected '&', expecting end of file in string code:1
Stack trace:
#0 %s(%d): ast\parse_code('%s', 15)
#1 {main}
ParseError: syntax error, unexpected '&', expecting end of file in file.php:1
Stack trace:
#0 %s(%d): ast\parse_code('%s', 15, 'file.php')
#1 {main}
