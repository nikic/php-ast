<?php
/**
 * PHPDoc stub file for ast extension
 * @author Bill Schaller <bill@zeroedin.com>
 * @author Nikita Popov <nikic@php.net>
 */

// AST KIND CONSTANTS
namespace ast;
const AST_ARG_LIST = 128;
const AST_LIST = 255;
const AST_ARRAY = 129;
const AST_ENCAPS_LIST = 130;
const AST_EXPR_LIST = 131;
const AST_STMT_LIST = 132;
const AST_IF = 133;
const AST_SWITCH_LIST = 134;
const AST_CATCH_LIST = 135;
const AST_PARAM_LIST = 136;
const AST_CLOSURE_USES = 137;
const AST_PROP_DECL = 138;
const AST_CONST_DECL = 139;
const AST_CLASS_CONST_DECL = 140;
const AST_NAME_LIST = 141;
const AST_TRAIT_ADAPTATIONS = 142;
const AST_USE = 143;
const AST_NAME = 2048;
const AST_CLOSURE_VAR = 2049;
const AST_NULLABLE_TYPE = 2050;
const AST_FUNC_DECL = 66;
const AST_CLOSURE = 67;
const AST_METHOD = 68;
const AST_CLASS = 69;
const AST_MAGIC_CONST = 0;
const AST_TYPE = 1;
const AST_VAR = 256;
const AST_CONST = 257;
const AST_UNPACK = 258;
const AST_UNARY_PLUS = 259;
const AST_UNARY_MINUS = 260;
const AST_CAST = 261;
const AST_EMPTY = 262;
const AST_ISSET = 263;
const AST_SILENCE = 264;
const AST_SHELL_EXEC = 265;
const AST_CLONE = 266;
const AST_EXIT = 267;
const AST_PRINT = 268;
const AST_INCLUDE_OR_EVAL = 269;
const AST_UNARY_OP = 270;
const AST_PRE_INC = 271;
const AST_PRE_DEC = 272;
const AST_POST_INC = 273;
const AST_POST_DEC = 274;
const AST_YIELD_FROM = 275;
const AST_GLOBAL = 276;
const AST_UNSET = 277;
const AST_RETURN = 278;
const AST_LABEL = 279;
const AST_REF = 280;
const AST_HALT_COMPILER = 281;
const AST_ECHO = 282;
const AST_THROW = 283;
const AST_GOTO = 284;
const AST_BREAK = 285;
const AST_CONTINUE = 286;
const AST_DIM = 512;
const AST_PROP = 513;
const AST_STATIC_PROP = 514;
const AST_CALL = 515;
const AST_CLASS_CONST = 516;
const AST_ASSIGN = 517;
const AST_ASSIGN_REF = 518;
const AST_ASSIGN_OP = 519;
const AST_BINARY_OP = 520;
const AST_GREATER = 521;
const AST_GREATER_EQUAL = 522;
const AST_AND = 523;
const AST_OR = 524;
const AST_ARRAY_ELEM = 525;
const AST_NEW = 526;
const AST_INSTANCEOF = 527;
const AST_YIELD = 528;
const AST_COALESCE = 529;
const AST_STATIC = 530;
const AST_WHILE = 531;
const AST_DO_WHILE = 532;
const AST_IF_ELEM = 533;
const AST_SWITCH = 534;
const AST_SWITCH_CASE = 535;
const AST_DECLARE = 536;
const AST_PROP_ELEM = 774;
const AST_CONST_ELEM = 775;
const AST_USE_TRAIT = 537;
const AST_TRAIT_PRECEDENCE = 538;
const AST_METHOD_REFERENCE = 539;
const AST_NAMESPACE = 540;
const AST_USE_ELEM = 541;
const AST_TRAIT_ALIAS = 542;
const AST_GROUP_USE = 543;
const AST_METHOD_CALL = 768;
const AST_STATIC_CALL = 769;
const AST_CONDITIONAL = 770;
const AST_TRY = 771;
const AST_CATCH = 772;
const AST_PARAM = 773;
const AST_FOR = 1024;
const AST_FOREACH = 1025;
// END AST KIND CONSTANTS

// AST FLAG CONSTANTS
namespace ast\flags;
const NAME_FQ = 0;
const NAME_NOT_FQ = 1;
const NAME_RELATIVE = 2;
const MODIFIER_PUBLIC = 256;
const MODIFIER_PROTECTED = 512;
const MODIFIER_PRIVATE = 1024;
const MODIFIER_STATIC = 1;
const MODIFIER_ABSTRACT = 2;
const MODIFIER_FINAL = 4;
const RETURNS_REF = 67108864;
const FUNC_RETURNS_REF = 67108864;
const FUNC_GENERATOR = 8388608;
const CLOSURE_USE_REF = 1;
const CLASS_ABSTRACT = 32;
const CLASS_FINAL = 4;
const CLASS_TRAIT = 128;
const CLASS_INTERFACE = 64;
const CLASS_ANONYMOUS = 256;
const PARAM_REF = 1;
const PARAM_VARIADIC = 2;
const TYPE_NULL = 1;
const TYPE_BOOL = 13;
const TYPE_LONG = 4;
const TYPE_DOUBLE = 5;
const TYPE_STRING = 6;
const TYPE_ARRAY = 7;
const TYPE_OBJECT = 8;
const TYPE_CALLABLE = 14;
const TYPE_VOID = 18;
const TYPE_ITERABLE = 19;
const UNARY_BOOL_NOT = 13;
const UNARY_BITWISE_NOT = 12;
const UNARY_SILENCE = 260;
const UNARY_PLUS = 261;
const UNARY_MINUS = 262;
const BINARY_BOOL_AND = 259;
const BINARY_BOOL_OR = 258;
const BINARY_BOOL_XOR = 14;
const BINARY_BITWISE_OR = 9;
const BINARY_BITWISE_AND = 10;
const BINARY_BITWISE_XOR = 11;
const BINARY_CONCAT = 8;
const BINARY_ADD = 1;
const BINARY_SUB = 2;
const BINARY_MUL = 3;
const BINARY_DIV = 4;
const BINARY_MOD = 5;
const BINARY_POW = 166;
const BINARY_SHIFT_LEFT = 6;
const BINARY_SHIFT_RIGHT = 7;
const BINARY_IS_IDENTICAL = 15;
const BINARY_IS_NOT_IDENTICAL = 16;
const BINARY_IS_EQUAL = 17;
const BINARY_IS_NOT_EQUAL = 18;
const BINARY_IS_SMALLER = 19;
const BINARY_IS_SMALLER_OR_EQUAL = 20;
const BINARY_IS_GREATER = 256;
const BINARY_IS_GREATER_OR_EQUAL = 257;
const BINARY_SPACESHIP = 170;
const BINARY_COALESCE = 260;
const ASSIGN_BITWISE_OR = 31;
const ASSIGN_BITWISE_AND = 32;
const ASSIGN_BITWISE_XOR = 33;
const ASSIGN_CONCAT = 30;
const ASSIGN_ADD = 23;
const ASSIGN_SUB = 24;
const ASSIGN_MUL = 25;
const ASSIGN_DIV = 26;
const ASSIGN_MOD = 27;
const ASSIGN_POW = 167;
const ASSIGN_SHIFT_LEFT = 28;
const ASSIGN_SHIFT_RIGHT = 29;
const EXEC_EVAL = 1;
const EXEC_INCLUDE = 2;
const EXEC_INCLUDE_ONCE = 4;
const EXEC_REQUIRE = 8;
const EXEC_REQUIRE_ONCE = 16;
const USE_NORMAL = 1;
const USE_FUNCTION = 2;
const USE_CONST = 4;
const MAGIC_LINE = 370;
const MAGIC_FILE = 371;
const MAGIC_DIR = 372;
const MAGIC_NAMESPACE = 389;
const MAGIC_FUNCTION = 376;
const MAGIC_METHOD = 375;
const MAGIC_CLASS = 373;
const MAGIC_TRAIT = 374;
const ARRAY_SYNTAX_LIST = 1;
const ARRAY_SYNTAX_LONG = 2;
const ARRAY_SYNTAX_SHORT = 3;
// END AST FLAG CONSTANTS

namespace ast;

/**
 * Parses code file and returns AST root node.
 *
 * @param string $filename Code file to parse
 * @param int    $version  AST version
 * @return Node Root node of AST
 *
 * @see https://github.com/nikic/php-ast for version information
 */
function parse_file($filename, $version)
{
}

/**
 * Parses code string and returns AST root node.
 *
 * @param string $code     Code string to parse
 * @param int    $version  AST version
 * @param string $filename Optional filename for use in parse errors
 * @return Node Root node of AST
 *
 * @see https://github.com/nikic/php-ast for version information
 */
function parse_code($code, $version, $filename = "string code")
{
}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return string String representation of AST kind value
 */
function get_kind_name($kind)
{
}

/**
 * @param int $kind AST_* constant value defining the kind of an AST node
 * @return bool Returns true if AST kind uses flags
 */
function kind_uses_flags($kind)
{
}

/**
 * Provides metadata for the AST kinds.
 *
 * The returned array is a map from AST kind to a Metadata object.
 *
 * @return Metadata[] Metadata about AST kinds
 */
function get_metadata()
{
}

/**
 * Returns currently supported AST versions.
 *
 * @param bool $exclude_deprecated Whether to exclude deprecated versions
 * @return int[] Array of supported AST versions
 */
function get_supported_versions($exclude_deprecated = false)
{
}

/**
 * This class describes a single node in a PHP AST.
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

    /** @var int Line the node starts in */
    public $lineno;

    /** @var array Child nodes (may be empty) */
    public $children;

    /**
     * A constructor which validates data types but not the values themselves.
     * For backwards compatibility reasons, all values are optional and properties default to null
     */
    public function __construct(int $kind = null, int $flags = null, array $children = null, int $lineno = null) {
        $this->kind = $kind;
        $this->flags = $flags;
        $this->children = $children;
        $this->lineno = $lineno;
    }
}

/**
 * Metadata entry for a single AST kind, as returned by ast\get_metadata().
 */
class Metadata
{
    /** @var int AST node kind (one of the ast\AST_* constants). */
    public $kind;

    /** @var string Name of the node kind (e.g. "AST_NAME"). */
    public $name;

    /**
     * @var string[] Array of supported flags. The flags are given as names of constants, such as
     *               "ast\flags\TYPE_STRING".
     */
    public $flags;

    /**
     * @var bool Whether the flags are exclusive or combinable. Exclusive flags should be checked
     *           using ===, while combinable flags should be checked using &. */
    public $flagsCombinable;
}

namespace ast\Node;

/**
 * AST Node type for function and class declarations.
 *
 * This class is no longer used in AST version 50 and higher.
 */
class Decl extends \ast\Node
{
    /** @var int End line number of the declaration */
    public $endLineno;

    /** @var string|null Name of the function or class (not including the namespace prefix). Optional for anonymous classes. */
    public $name;

    /** @var string|null Doc comment preceding the declaration. null if no doc comment was used. */
    public $docComment;
}
