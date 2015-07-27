<?php declare(strict_types=1);

use ast\flags;

const AST_DUMP_LINENOS = 1;

function get_flag_info() : array {
    static $exclusive, $combinable;
    if ($exclusive !== null) {
        return [$exclusive, $combinable];
    }

    $modifiers = [
        flags\MODIFIER_PUBLIC => 'MODIFIER_PUBLIC',
        flags\MODIFIER_PROTECTED => 'MODIFIER_PROTECTED',
        flags\MODIFIER_PRIVATE => 'MODIFIER_PRIVATE',
        flags\MODIFIER_STATIC => 'MODIFIER_STATIC',
        flags\MODIFIER_ABSTRACT => 'MODIFIER_ABSTRACT',
        flags\MODIFIER_FINAL => 'MODIFIER_FINAL',
        flags\RETURNS_REF => 'RETURNS_REF',
    ];
    $types = [
        flags\TYPE_NULL => 'TYPE_NULL',
        flags\TYPE_BOOL => 'TYPE_BOOL',
        flags\TYPE_LONG => 'TYPE_LONG',
        flags\TYPE_DOUBLE => 'TYPE_DOUBLE',
        flags\TYPE_STRING => 'TYPE_STRING',
        flags\TYPE_ARRAY => 'TYPE_ARRAY',
        flags\TYPE_OBJECT => 'TYPE_OBJECT',
        flags\TYPE_CALLABLE => 'TYPE_CALLABLE',
    ];
    $useTypes = [
        T_CLASS => 'T_CLASS',
        T_FUNCTION => 'T_FUNCTION',
        T_CONST => 'T_CONST',
    ];

    $exclusive = [
        ast\AST_NAME => [
            flags\NAME_FQ => 'NAME_FQ',
            flags\NAME_NOT_FQ => 'NAME_NOT_FQ',
            flags\NAME_RELATIVE => 'NAME_RELATIVE',
        ],
        ast\AST_CLASS => [
            flags\CLASS_ABSTRACT => 'CLASS_ABSTRACT',
            flags\CLASS_FINAL => 'CLASS_FINAL',
            flags\CLASS_TRAIT => 'CLASS_TRAIT',
            flags\CLASS_INTERFACE => 'CLASS_INTERFACE',
        ],
        ast\AST_PARAM => [
            flags\PARAM_REF => 'PARAM_REF',
            flags\PARAM_VARIADIC => 'PARAM_VARIADIC',
        ],
        ast\AST_TYPE => $types,
        ast\AST_CAST => $types,
        ast\AST_UNARY_OP => [
            flags\UNARY_BOOL_NOT => 'UNARY_BOOL_NOT',
            flags\UNARY_BITWISE_NOT => 'UNARY_BITWISE_NOT',
        ],
        ast\AST_BINARY_OP => [
            flags\BINARY_BOOL_XOR => 'BINARY_BOOL_XOR',
            flags\BINARY_BITWISE_OR => 'BINARY_BITWISE_OR',
            flags\BINARY_BITWISE_AND => 'BINARY_BITWISE_AND',
            flags\BINARY_BITWISE_XOR => 'BINARY_BITWISE_XOR',
            flags\BINARY_CONCAT => 'BINARY_CONCAT',
            flags\BINARY_ADD => 'BINARY_ADD',
            flags\BINARY_SUB => 'BINARY_SUB',
            flags\BINARY_MUL => 'BINARY_MUL',
            flags\BINARY_DIV => 'BINARY_DIV',
            flags\BINARY_MOD => 'BINARY_MOD',
            flags\BINARY_POW => 'BINARY_POW',
            flags\BINARY_SHIFT_LEFT => 'BINARY_SHIFT_LEFT',
            flags\BINARY_SHIFT_RIGHT => 'BINARY_SHIFT_RIGHT',
            flags\BINARY_IS_IDENTICAL => 'BINARY_IS_IDENTICAL',
            flags\BINARY_IS_NOT_IDENTICAL => 'BINARY_IS_NOT_IDENTICAL',
            flags\BINARY_IS_EQUAL => 'BINARY_IS_EQUAL',
            flags\BINARY_IS_NOT_EQUAL => 'BINARY_IS_NOT_EQUAL',
            flags\BINARY_IS_SMALLER => 'BINARY_IS_SMALLER',
            flags\BINARY_IS_SMALLER_OR_EQUAL => 'BINARY_IS_SMALLER_OR_EQUAL',
            flags\BINARY_SPACESHIP => 'BINARY_SPACESHIP',
        ],
        ast\AST_ASSIGN_OP => [
            flags\ASSIGN_BITWISE_OR => 'ASSIGN_BITWISE_OR',
            flags\ASSIGN_BITWISE_AND => 'ASSIGN_BITWISE_AND',
            flags\ASSIGN_BITWISE_XOR => 'ASSIGN_BITWISE_XOR',
            flags\ASSIGN_CONCAT => 'ASSIGN_CONCAT',
            flags\ASSIGN_ADD => 'ASSIGN_ADD',
            flags\ASSIGN_SUB => 'ASSIGN_SUB',
            flags\ASSIGN_MUL => 'ASSIGN_MUL',
            flags\ASSIGN_DIV => 'ASSIGN_DIV',
            flags\ASSIGN_MOD => 'ASSIGN_MOD',
            flags\ASSIGN_POW => 'ASSIGN_POW',
            flags\ASSIGN_SHIFT_LEFT => 'ASSIGN_SHIFT_LEFT',
            flags\ASSIGN_SHIFT_RIGHT => 'ASSIGN_SHIFT_RIGHT',
        ],
        ast\AST_MAGIC_CONST => [
            T_LINE => 'T_LINE',
            T_FILE => 'T_FILE',
            T_DIR => 'T_DIR',
            T_TRAIT_C => 'T_TRAIT_C',
            T_METHOD_C => 'T_METHOD_C',
            T_FUNC_C => 'T_FUNC_C',
            T_NS_C => 'T_NS_C',
            T_CLASS_C => 'T_CLASS_C',
        ],
        ast\AST_USE => $useTypes,
        ast\AST_GROUP_USE => $useTypes,
        ast\AST_USE_ELEM => $useTypes,
        ast\AST_INCLUDE_OR_EVAL => [
            flags\EXEC_EVAL => 'EXEC_EVAL',
            flags\EXEC_INCLUDE => 'EXEC_INCLUDE',
            flags\EXEC_INCLUDE_ONCE => 'EXEC_INCLUDE_ONCE',
            flags\EXEC_REQUIRE => 'EXEC_REQUIRE',
            flags\EXEC_REQUIRE_ONCE => 'EXEC_REQUIRE_ONCE',
        ],
    ];

    $combinable = [];
    $combinable[ast\AST_METHOD] = $combinable[ast\AST_FUNC_DECL] = $combinable[ast\AST_CLOSURE]
        = $combinable[ast\AST_PROP_DECL] = $combinable[ast\AST_TRAIT_ALIAS] = $modifiers;

    return [$exclusive, $combinable];
}

function format_flags(int $kind, int $flags) : string {
    list($exclusive, $combinable) = get_flag_info();
    if (isset($exclusive[$kind])) {
        $flagInfo = $exclusive[$kind];
        if (isset($flagInfo[$flags])) {
            return "{$flagInfo[$flags]} ($flags)";
        }
    } else if (isset($combinable[$kind])) {
        $flagInfo = $combinable[$kind];
        $names = [];
        foreach ($flagInfo as $flag => $name) {
            if ($flags & $flag) {
                $names[] = $name;
            }
        }
        if (!empty($names)) {
            return implode(" | ", $names) . " ($flags)";
        }
    }
    return (string) $flags;
}

/** Dumps abstract syntax tree */
function ast_dump($ast, int $options = 0) : string {
    if ($ast instanceof ast\Node) {
        $result = ast\get_kind_name($ast->kind);

        if ($options & AST_DUMP_LINENOS) {
            $result .= " @ $ast->lineno";
            if (isset($ast->endLineno)) {
                $result .= "-$ast->endLineno";
            }
        }

        if (ast\kind_uses_flags($ast->kind)) {
            $result .= "\n    flags: " . format_flags($ast->kind, $ast->flags);
        }
        if (isset($ast->name)) {
            $result .= "\n    name: $ast->name";
        }
        if (isset($ast->docComment)) {
            $result .= "\n    docComment: $ast->docComment";
        }
        foreach ($ast->children as $i => $child) {
            $result .= "\n    $i: " . str_replace("\n", "\n    ", ast_dump($child, $options));
        }
        return $result;
    } else if ($ast === null) {
        return 'null';
    } else if (is_string($ast)) {
        return "\"$ast\""; 
    } else {
        return (string) $ast;
    }
}
