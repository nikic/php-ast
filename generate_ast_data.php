<?php

$code = <<<EOC
#include "php_ast.h"

const size_t ast_kinds_count = {COUNT};

const zend_ast_kind ast_kinds[] = {
{KINDS}
};

const char *ast_kind_to_name(zend_ast_kind kind) {
\tswitch (kind) {
{STRS}
\t}

\treturn NULL;
}

void ast_register_kind_constants(INIT_FUNC_ARGS) {
{CONSTS}
}
EOC;

if ($argc != 2) {
    die("Must provide input file\n");
}

$inFile = $argv[1];
$outFile = __DIR__ . '/ast_data.c';

if (!is_readable($inFile)) {
    die("Input file not readable\n");
}

$inCode = file_get_contents($inFile);
if (!preg_match('/enum _zend_ast_kind \{(.*?)\};/s', $inCode, $matches)) {
    die("Malformed input file\n");
}

$data = [];
$lines = explode("\n", $matches[1]);

foreach ($lines as $line) {
    if (!preg_match('/\s*(ZEND_([A-Z_]+))/', $line, $matches)) {
        continue;
    }

    list(, $zend_name, $name) = $matches;
    if ($name == 'AST_ZNODE' || $name == 'AST_ZVAL') {
        continue;
    }

    $data[$zend_name] = $name;
}

$data['AST_NAME'] = 'AST_NAME';

$kinds = [];
$strs = [];
$consts = [];
foreach ($data as $zend_name => $name) {
    $kinds[] = "\t$zend_name,";
    $strs[] = "\t\tcase $zend_name: return \"$name\";";
    $consts[] = "\tREGISTER_NS_LONG_CONSTANT(\"ast\", \"$name\", $zend_name,"
        . " CONST_CS | CONST_PERSISTENT );";
}

$code = str_replace('{COUNT}', count($data), $code);
$code = str_replace('{KINDS}', implode("\n", $kinds), $code);
$code = str_replace('{STRS}', implode("\n", $strs), $code);
$code = str_replace('{CONSTS}', implode("\n", $consts), $code);

file_put_contents($outFile, $code);
