<?php
/**
 * PHPDoc stub file for ast extension
 * @author Bill Schaller <bill@zeroedin.com>
 * @author Nikita Popov <nikic@php.net>
 */

namespace ast;

// AST CONSTANTS
/* Dumped using command line:
 * php.exe -r "foreach(get_defined_constants(true)['ast'] as $constant => $value) {echo \"define('$constant', $value);\n\";}"
 */
// AST KIND CONSTANTS
define('ast\AST_FUNC_DECL', 66);
define('ast\AST_CLOSURE', 67);
define('ast\AST_METHOD', 68);
define('ast\AST_CLASS', 69);
define('ast\AST_ARG_LIST', 128);
define('ast\AST_LIST', 129);
define('ast\AST_ARRAY', 130);
define('ast\AST_ENCAPS_LIST', 131);
define('ast\AST_EXPR_LIST', 132);
define('ast\AST_STMT_LIST', 133);
define('ast\AST_IF', 134);
define('ast\AST_SWITCH_LIST', 135);
define('ast\AST_CATCH_LIST', 136);
define('ast\AST_PARAM_LIST', 137);
define('ast\AST_CLOSURE_USES', 138);
define('ast\AST_PROP_DECL', 139);
define('ast\AST_CONST_DECL', 140);
define('ast\AST_CLASS_CONST_DECL', 141);
define('ast\AST_NAME_LIST', 142);
define('ast\AST_TRAIT_ADAPTATIONS', 143);
define('ast\AST_USE', 144);
define('ast\AST_MAGIC_CONST', 0);
define('ast\AST_TYPE', 1);
define('ast\AST_VAR', 256);
define('ast\AST_CONST', 257);
define('ast\AST_UNPACK', 258);
define('ast\AST_UNARY_PLUS', 259);
define('ast\AST_UNARY_MINUS', 260);
define('ast\AST_CAST', 261);
define('ast\AST_EMPTY', 262);
define('ast\AST_ISSET', 263);
define('ast\AST_SILENCE', 264);
define('ast\AST_SHELL_EXEC', 265);
define('ast\AST_CLONE', 266);
define('ast\AST_EXIT', 267);
define('ast\AST_PRINT', 268);
define('ast\AST_INCLUDE_OR_EVAL', 269);
define('ast\AST_UNARY_OP', 270);
define('ast\AST_PRE_INC', 271);
define('ast\AST_PRE_DEC', 272);
define('ast\AST_POST_INC', 273);
define('ast\AST_POST_DEC', 274);
define('ast\AST_YIELD_FROM', 275);
define('ast\AST_GLOBAL', 276);
define('ast\AST_UNSET', 277);
define('ast\AST_RETURN', 278);
define('ast\AST_LABEL', 279);
define('ast\AST_REF', 280);
define('ast\AST_HALT_COMPILER', 281);
define('ast\AST_ECHO', 282);
define('ast\AST_THROW', 283);
define('ast\AST_GOTO', 284);
define('ast\AST_BREAK', 285);
define('ast\AST_CONTINUE', 286);
define('ast\AST_DIM', 512);
define('ast\AST_PROP', 513);
define('ast\AST_STATIC_PROP', 514);
define('ast\AST_CALL', 515);
define('ast\AST_CLASS_CONST', 516);
define('ast\AST_ASSIGN', 517);
define('ast\AST_ASSIGN_REF', 518);
define('ast\AST_ASSIGN_OP', 519);
define('ast\AST_BINARY_OP', 520);
define('ast\AST_GREATER', 521);
define('ast\AST_GREATER_EQUAL', 522);
define('ast\AST_AND', 523);
define('ast\AST_OR', 524);
define('ast\AST_ARRAY_ELEM', 525);
define('ast\AST_NEW', 526);
define('ast\AST_INSTANCEOF', 527);
define('ast\AST_YIELD', 528);
define('ast\AST_COALESCE', 529);
define('ast\AST_STATIC', 530);
define('ast\AST_WHILE', 531);
define('ast\AST_DO_WHILE', 532);
define('ast\AST_IF_ELEM', 533);
define('ast\AST_SWITCH', 534);
define('ast\AST_SWITCH_CASE', 535);
define('ast\AST_DECLARE', 536);
define('ast\AST_CONST_ELEM', 537);
define('ast\AST_USE_TRAIT', 538);
define('ast\AST_TRAIT_PRECEDENCE', 539);
define('ast\AST_METHOD_REFERENCE', 540);
define('ast\AST_NAMESPACE', 541);
define('ast\AST_USE_ELEM', 542);
define('ast\AST_TRAIT_ALIAS', 543);
define('ast\AST_GROUP_USE', 544);
define('ast\AST_METHOD_CALL', 768);
define('ast\AST_STATIC_CALL', 769);
define('ast\AST_CONDITIONAL', 770);
define('ast\AST_TRY', 771);
define('ast\AST_CATCH', 772);
define('ast\AST_PARAM', 773);
define('ast\AST_PROP_ELEM', 774);
define('ast\AST_FOR', 1024);
define('ast\AST_FOREACH', 1025);
define('ast\AST_NAME', 2048);
define('ast\AST_CLOSURE_VAR', 2049);

// FLAG CONSTANTS
define('ast\flags\NAME_FQ', 0);
define('ast\flags\NAME_NOT_FQ', 1);
define('ast\flags\NAME_RELATIVE', 2);
define('ast\flags\MODIFIER_PUBLIC', 256);
define('ast\flags\MODIFIER_PROTECTED', 512);
define('ast\flags\MODIFIER_PRIVATE', 1024);
define('ast\flags\MODIFIER_STATIC', 1);
define('ast\flags\MODIFIER_ABSTRACT', 2);
define('ast\flags\MODIFIER_FINAL', 4);
define('ast\flags\RETURNS_REF', 67108864);
define('ast\flags\CLASS_ABSTRACT', 32);
define('ast\flags\CLASS_FINAL', 4);
define('ast\flags\CLASS_TRAIT', 128);
define('ast\flags\CLASS_INTERFACE', 64);
define('ast\flags\CLASS_ANONYMOUS', 256);
define('ast\flags\PARAM_REF', 1);
define('ast\flags\PARAM_VARIADIC', 2);
define('ast\flags\TYPE_NULL', 1);
define('ast\flags\TYPE_BOOL', 13);
define('ast\flags\TYPE_LONG', 4);
define('ast\flags\TYPE_DOUBLE', 5);
define('ast\flags\TYPE_STRING', 6);
define('ast\flags\TYPE_ARRAY', 7);
define('ast\flags\TYPE_OBJECT', 8);
define('ast\flags\TYPE_CALLABLE', 14);
define('ast\flags\UNARY_BOOL_NOT', 13);
define('ast\flags\UNARY_BITWISE_NOT', 12);
define('ast\flags\UNARY_SILENCE', 260);
define('ast\flags\UNARY_PLUS', 261);
define('ast\flags\UNARY_MINUS', 262);
define('ast\flags\BINARY_BOOL_AND', 259);
define('ast\flags\BINARY_BOOL_OR', 258);
define('ast\flags\BINARY_BOOL_XOR', 14);
define('ast\flags\BINARY_BITWISE_OR', 9);
define('ast\flags\BINARY_BITWISE_AND', 10);
define('ast\flags\BINARY_BITWISE_XOR', 11);
define('ast\flags\BINARY_CONCAT', 8);
define('ast\flags\BINARY_ADD', 1);
define('ast\flags\BINARY_SUB', 2);
define('ast\flags\BINARY_MUL', 3);
define('ast\flags\BINARY_DIV', 4);
define('ast\flags\BINARY_MOD', 5);
define('ast\flags\BINARY_POW', 166);
define('ast\flags\BINARY_SHIFT_LEFT', 6);
define('ast\flags\BINARY_SHIFT_RIGHT', 7);
define('ast\flags\BINARY_IS_IDENTICAL', 15);
define('ast\flags\BINARY_IS_NOT_IDENTICAL', 16);
define('ast\flags\BINARY_IS_EQUAL', 17);
define('ast\flags\BINARY_IS_NOT_EQUAL', 18);
define('ast\flags\BINARY_IS_SMALLER', 19);
define('ast\flags\BINARY_IS_SMALLER_OR_EQUAL', 20);
define('ast\flags\BINARY_IS_GREATER', 256);
define('ast\flags\BINARY_IS_GREATER_OR_EQUAL', 257);
define('ast\flags\BINARY_SPACESHIP', 170);
define('ast\flags\ASSIGN_BITWISE_OR', 31);
define('ast\flags\ASSIGN_BITWISE_AND', 32);
define('ast\flags\ASSIGN_BITWISE_XOR', 33);
define('ast\flags\ASSIGN_CONCAT', 30);
define('ast\flags\ASSIGN_ADD', 23);
define('ast\flags\ASSIGN_SUB', 24);
define('ast\flags\ASSIGN_MUL', 25);
define('ast\flags\ASSIGN_DIV', 26);
define('ast\flags\ASSIGN_MOD', 27);
define('ast\flags\ASSIGN_POW', 167);
define('ast\flags\ASSIGN_SHIFT_LEFT', 28);
define('ast\flags\ASSIGN_SHIFT_RIGHT', 29);
define('ast\flags\EXEC_EVAL', 1);
define('ast\flags\EXEC_INCLUDE', 2);
define('ast\flags\EXEC_INCLUDE_ONCE', 4);
define('ast\flags\EXEC_REQUIRE', 8);
define('ast\flags\EXEC_REQUIRE_ONCE', 16);
define('ast\flags\USE_NORMAL', 361);
define('ast\flags\USE_FUNCTION', 346);
define('ast\flags\USE_CONST', 347);
define('ast\flags\MAGIC_LINE', 370);
define('ast\flags\MAGIC_FILE', 371);
define('ast\flags\MAGIC_DIR', 372);
define('ast\flags\MAGIC_NAMESPACE', 389);
define('ast\flags\MAGIC_FUNCTION', 376);
define('ast\flags\MAGIC_METHOD', 375);
define('ast\flags\MAGIC_CLASS', 373);
define('ast\flags\MAGIC_TRAIT', 374);

// AST FUNCTION PROTOTYPES
/**
 * @param string $filename Code file to parse
 * @param int $version API version
 * @return Node Root node of AST
 * @see https://github.com/nikic/php-ast for version information
 */
function parse_file($filename, $version)
{
}

/**
 * Parses code string and returns AST root node.
 * @param string $code Code string to parse
 * @param int $version API version
 * @param string $filename Optional - specifies filename of parsed file
 * @return Node Root node of AST
 * @see https://github.com/nikic/php-ast for version information
 */
function parse_code($code, $version, $filename = "string code")
{
}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return string string representation of AST kind value
 */
function get_kind_name($kind)
{
}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return bool returns true if AST kind uses flags
 */
function kind_uses_flags($kind)
{
}

/**
 * This class describes a single node in a PHP AST.
 * @package ast
 */
class Node
{
    /** @var int AST Node Kind. Values are one of ast\AST_* constants. */
    public $kind;

    /**
     * @var int AST Flags.
     * Certain node kinds have flags that can be set.
     * These will be a bitfield of ast\flags\* constants.
     */
    public $flags;

    /** @var int source line number */
    public $lineno;

    /** @var array Child nodes, if any exist */
    public $children;
}

namespace ast\Node;

/**
 * AST Node type for function and class declarations.
 * Class Decl
 * @package ast\Node
 */
class Decl extends \ast\Node
{
    /** @var int end line number of the declaration */
    public $endLineno;

    /** @var string name of the function or class */
    public $name;

    /** @var string|null doc comment preceeding the declaration. null if no doc comment was used. */
    public $docComment;
}
