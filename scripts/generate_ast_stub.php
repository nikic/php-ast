<?php

$stubFile = __DIR__ . '/../ast_stub.php';
if (!extension_loaded('ast')) {
    fwrite(STDERR, __FILE__ . " requires that php-ast be enabled\n");
    exit(1);
}

$stub = file_get_contents($stubFile);
$stub = preg_replace_callback(
    '~(?<=// AST KIND CONSTANTS\n).*(?=\n// END AST KIND CONSTANTS)~s',
    function ($matches) {
        $consts = "namespace ast;";
        foreach (get_defined_constants(true)['ast'] as $name => $value) {
            if (0 === strpos($name, 'ast\\AST_')) {
                $consts .= "\nconst " . substr($name, 4) . " = $value;";
            }
        }
        return $consts;
    },
    $stub
);
$stub = preg_replace_callback(
    '~(?<=// AST FLAG CONSTANTS\n).*(?=\n// END AST FLAG CONSTANTS)~s',
    function ($matches) {
        $consts = "namespace ast\\flags;";
        foreach (get_defined_constants(true)['ast'] as $name => $value) {
            if (0 === strpos($name, 'ast\\flags\\')) {
                $consts .= "\nconst " . substr($name, 10) . " = $value;";
            }
        }
        return $consts;
    },
    $stub
);
file_put_contents($stubFile, $stub);
