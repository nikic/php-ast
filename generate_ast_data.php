<?php error_reporting(E_ALL);

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

zend_string *ast_kind_child_name(zend_ast_kind kind, uint32_t child) {
\tswitch (kind) {
{CHILD_NAMES}
\t}

\treturn NULL;
}

void ast_register_kind_constants(INIT_FUNC_ARGS) {
{CONSTS}
}

EOC;

$strDefsHeader = <<<EOC
#ifndef AST_STR_DEFS_H
#define AST_STR_DEFS_H

#define AST_STR_DEFS \
{STR_DEFS}

#endif

EOC;

$funcNames = ['params', 'uses', 'stmts', 'returnType'];

$names = [
    /* special nodes */
    'AST_NAME' => ['name'],
    'AST_CLOSURE_VAR' => ['name'],

    /* declaration nodes */
    'ZEND_AST_FUNC_DECL' => $funcNames,
    'ZEND_AST_CLOSURE' => $funcNames,
    'ZEND_AST_METHOD' => $funcNames,
    'ZEND_AST_CLASS' => ['extends', 'implements', 'stmts'],

    /* 0 child nodes */
    'ZEND_AST_MAGIC_CONST' => [],
    'ZEND_AST_TYPE' => [],

    /* 1 child node */
    'ZEND_AST_VAR' => ['name'],
    'ZEND_AST_CONST' => ['name'],
    'ZEND_AST_UNPACK' => ['expr'],
    'ZEND_AST_UNARY_PLUS' => ['expr'],
    'ZEND_AST_UNARY_MINUS' => ['expr'],
    'ZEND_AST_CAST' => ['expr'],
    'ZEND_AST_EMPTY' => ['expr'],
    'ZEND_AST_ISSET' => ['var'],
    'ZEND_AST_SILENCE' => ['expr'],
    'ZEND_AST_SHELL_EXEC' => ['expr'],
    'ZEND_AST_CLONE' => ['expr'],
    'ZEND_AST_EXIT' => ['expr'],
    'ZEND_AST_PRINT' => ['expr'],
    'ZEND_AST_INCLUDE_OR_EVAL' => ['expr'],
    'ZEND_AST_UNARY_OP' => ['expr'],
    'ZEND_AST_PRE_INC' => ['var'],
    'ZEND_AST_PRE_DEC' => ['var'],
    'ZEND_AST_POST_INC' => ['var'],
    'ZEND_AST_POST_DEC' => ['var'],
    'ZEND_AST_YIELD_FROM' => ['expr'],

    'ZEND_AST_GLOBAL' => ['var'],
    'ZEND_AST_UNSET' => ['var'],
    'ZEND_AST_RETURN' => ['expr'],
    'ZEND_AST_LABEL' => ['name'],
    'ZEND_AST_REF' => ['var'],
    'ZEND_AST_HALT_COMPILER' => ['offset'],
    'ZEND_AST_ECHO' => ['expr'],
    'ZEND_AST_THROW' => ['expr'],
    'ZEND_AST_GOTO' => ['label'],
    'ZEND_AST_BREAK' => ['depth'],
    'ZEND_AST_CONTINUE' => ['depth'],

    /* 2 child nodes */
    'ZEND_AST_DIM' => ['expr', 'dim'],
    'ZEND_AST_PROP' => ['expr', 'prop'],
    'ZEND_AST_STATIC_PROP' => ['class', 'prop'],
    'ZEND_AST_CALL' => ['expr', 'args'],
    'ZEND_AST_CLASS_CONST' => ['class', 'const'],
    'ZEND_AST_ASSIGN' => ['var', 'expr'],
    'ZEND_AST_ASSIGN_REF' => ['var', 'expr'],
    'ZEND_AST_ASSIGN_OP' => ['var', 'expr'],
    'ZEND_AST_BINARY_OP' => ['left', 'right'],
    'ZEND_AST_GREATER' => ['left', 'right'],
    'ZEND_AST_GREATER_EQUAL' => ['left', 'right'],
    'ZEND_AST_AND' => ['left', 'right'],
    'ZEND_AST_OR' => ['left', 'right'],
    'ZEND_AST_ARRAY_ELEM' => ['value', 'key'],
    'ZEND_AST_NEW' => ['class', 'args'],
    'ZEND_AST_INSTANCEOF' => ['expr', 'class'],
    'ZEND_AST_YIELD' => ['value', 'key'],
    'ZEND_AST_COALESCE' => ['left', 'right'],

    'ZEND_AST_STATIC' => ['var', 'default'],
    'ZEND_AST_WHILE' => ['cond', 'stmts'],
    'ZEND_AST_DO_WHILE' => ['stmts', 'cond'],
    'ZEND_AST_IF_ELEM' => ['cond', 'stmts'],
    'ZEND_AST_SWITCH' => ['cond', 'stmts'],
    'ZEND_AST_SWITCH_CASE' => ['cond', 'stmts'],
    'ZEND_AST_DECLARE' => ['declares', 'stmts'],
    'ZEND_AST_PROP_ELEM' => ['var', 'default'],
    'ZEND_AST_CONST_ELEM' => ['name', 'value'],
    'ZEND_AST_USE_TRAIT' => ['traits', 'adaptations'],
    'ZEND_AST_TRAIT_PRECEDENCE' => ['method', 'insteadof'],
    'ZEND_AST_METHOD_REFERENCE' => ['class', 'method'],
    'ZEND_AST_NAMESPACE' => ['name', 'stmts'],
    'ZEND_AST_USE_ELEM' => ['name', 'alias'],
    'ZEND_AST_TRAIT_ALIAS' => ['method', 'alias'],
    'ZEND_AST_GROUP_USE' => ['prefix', 'uses'],

    /* 3 child nodes */
    'ZEND_AST_METHOD_CALL' => ['expr', 'method', 'args'],
    'ZEND_AST_STATIC_CALL' => ['class', 'method', 'args'],
    'ZEND_AST_CONDITIONAL' => ['cond', 'trueExpr', 'falseExpr'],

    'ZEND_AST_TRY' => ['tryStmts', 'catches', 'finallyStmts'],
    'ZEND_AST_CATCH' => ['exception', 'var', 'stmts'],
    'ZEND_AST_PARAM' => ['type', 'var', 'default'],

    /* 4 child nodes */
    'ZEND_AST_FOR' => ['init', 'cond', 'loop', 'stmts'],
    'ZEND_AST_FOREACH' => ['expr', 'valueVar', 'keyVar', 'stmts'],
];

if ($argc != 2) {
    die("Must provide input file\n");
}

$inFile = $argv[1];
$outCodeFile = __DIR__ . '/ast_data.c';
$strDefsFile = __DIR__ . '/ast_str_defs.h';

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
$data['AST_CLOSURE_VAR'] = 'AST_CLOSURE_VAR';

$kinds = [];
$strs = [];
$consts = [];
foreach ($data as $zend_name => $name) {
    $kinds[] = "\t$zend_name,";
    $strs[] = "\t\tcase $zend_name: return \"$name\";";
    $consts[] = "\tREGISTER_NS_LONG_CONSTANT(\"ast\", \"$name\", $zend_name,"
        . " CONST_CS | CONST_PERSISTENT);";
}

$code = str_replace('{COUNT}', count($data), $code);
$code = str_replace('{KINDS}', implode("\n", $kinds), $code);
$code = str_replace('{STRS}', implode("\n", $strs), $code);
$code = str_replace('{CONSTS}', implode("\n", $consts), $code);

$childNames = [];
foreach ($names as $kind => $children) {
    if (empty($children)) {
        $childNames[] = "\t\tcase $kind:\n\t\t\treturn NULL;";
        continue;
    }

    $kindChildNames = [];
    foreach ($children as $index => $name) {
        $kindChildNames[] = "\t\t\t\tcase $index: return AST_STR($name);";
    }
    $childNames[] = "\t\tcase $kind:\n\t\t\tswitch (child) {\n"
        . implode("\n", $kindChildNames) . "\n\t\t\t}\n\t\t\treturn NULL;";
}
$code = str_replace('{CHILD_NAMES}', implode("\n", $childNames), $code);

file_put_contents($outCodeFile, $code);

$strings = get_possible_strings($names);
$strDefs = [];
foreach ($strings as $name) {
    $strDefs[] .= "\tX($name) \\";
}

$strDefsHeader = str_replace('{STR_DEFS}', implode("\n", $strDefs), $strDefsHeader);
file_put_contents($strDefsFile, $strDefsHeader);

function get_possible_strings(array $spec) {
    $strings = array_fill_keys([
        'kind', 'flags', 'lineno', 'children',
        'name', 'docComment', 'endLineno'
    ], true);

    foreach ($spec as $kind => $children) {
        foreach ($children as $childName) {
            $strings[$childName] = true;
        }
    }
    return array_keys($strings);
}
