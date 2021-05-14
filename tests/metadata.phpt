--TEST--
AST metadata
--FILE--
<?php

$metadata = ast\get_metadata();
foreach ($metadata as $data) {
    $flags = [];
    foreach ($data->flags as $flag) {
        $flags[] = substr($flag, strrpos($flag, '\\') + 1);
    }
    $metadataHasFlags = count($flags) > 0;
    $kindUsesFlags = ast\kind_uses_flags($data->kind);
    if ($metadataHasFlags != $kindUsesFlags) {
        echo "kind_uses_flags for $data->name is unexpectedly " . var_export($kindUsesFlags, true) . "\n";
    }

    echo "$data->name: ";
    if ($data->flagsCombinable) {
        echo "(combinable) ";
    }
    echo "[", implode(", ", $flags), "]\n";
}

// NOTE: AST_PARAM has overlapping flag values for MODIFIER_PUBLIC and PARAM_REF in php 7.4.
// To work around this, MODIFIER_* were omitted from get_metadata in 7.4 and older.
?>
--EXPECTF--
AST_ARG_LIST: []
AST_LIST: []
AST_ARRAY: [ARRAY_SYNTAX_LIST, ARRAY_SYNTAX_LONG, ARRAY_SYNTAX_SHORT]
AST_ENCAPS_LIST: []
AST_EXPR_LIST: []
AST_STMT_LIST: []
AST_IF: []
AST_SWITCH_LIST: []
AST_CATCH_LIST: []
AST_PARAM_LIST: []
AST_CLOSURE_USES: []
AST_PROP_DECL: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL]
AST_CONST_DECL: []
AST_CLASS_CONST_DECL: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE]
AST_NAME_LIST: []
AST_TRAIT_ADAPTATIONS: []
AST_USE: [USE_NORMAL, USE_FUNCTION, USE_CONST]
AST_TYPE_UNION: []
AST_ATTRIBUTE_LIST: []
AST_ATTRIBUTE_GROUP: []
AST_MATCH_ARM_LIST: []
AST_NAME: [NAME_FQ, NAME_NOT_FQ, NAME_RELATIVE]
AST_CLOSURE_VAR: [CLOSURE_USE_REF]
AST_NULLABLE_TYPE: []
AST_FUNC_DECL: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL, FUNC_RETURNS_REF, FUNC_GENERATOR]
AST_CLOSURE: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL, FUNC_RETURNS_REF, FUNC_GENERATOR]
AST_METHOD: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL, FUNC_RETURNS_REF, FUNC_GENERATOR]
AST_ARROW_FUNC: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL, FUNC_RETURNS_REF, FUNC_GENERATOR]
AST_CLASS: (combinable) [CLASS_ABSTRACT, CLASS_FINAL, CLASS_TRAIT, CLASS_INTERFACE, CLASS_ANONYMOUS, CLASS_ENUM]
AST_MAGIC_CONST: [MAGIC_LINE, MAGIC_FILE, MAGIC_DIR, MAGIC_NAMESPACE, MAGIC_FUNCTION, MAGIC_METHOD, MAGIC_CLASS, MAGIC_TRAIT]
AST_TYPE: [TYPE_NULL, TYPE_FALSE, TYPE_BOOL, TYPE_LONG, TYPE_DOUBLE, TYPE_STRING, TYPE_ARRAY, TYPE_OBJECT, TYPE_CALLABLE, TYPE_VOID, TYPE_ITERABLE, TYPE_STATIC, TYPE_MIXED, TYPE_NEVER]
AST_VAR: []
AST_CONST: []
AST_UNPACK: []
AST_CAST: [TYPE_NULL, TYPE_FALSE, TYPE_BOOL, TYPE_LONG, TYPE_DOUBLE, TYPE_STRING, TYPE_ARRAY, TYPE_OBJECT, TYPE_CALLABLE, TYPE_VOID, TYPE_ITERABLE, TYPE_STATIC, TYPE_MIXED, TYPE_NEVER]
AST_EMPTY: []
AST_ISSET: []
AST_SHELL_EXEC: []
AST_CLONE: []
AST_EXIT: []
AST_PRINT: []
AST_INCLUDE_OR_EVAL: [EXEC_EVAL, EXEC_INCLUDE, EXEC_INCLUDE_ONCE, EXEC_REQUIRE, EXEC_REQUIRE_ONCE]
AST_UNARY_OP: [UNARY_BOOL_NOT, UNARY_BITWISE_NOT, UNARY_MINUS, UNARY_PLUS, UNARY_SILENCE]
AST_PRE_INC: []
AST_PRE_DEC: []
AST_POST_INC: []
AST_POST_DEC: []
AST_YIELD_FROM: []
AST_GLOBAL: []
AST_UNSET: []
AST_RETURN: []
AST_LABEL: []
AST_REF: []
AST_HALT_COMPILER: []
AST_ECHO: []
AST_THROW: []
AST_GOTO: []
AST_BREAK: []
AST_CONTINUE: []
AST_CLASS_NAME: []
AST_CLASS_CONST_GROUP: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE]
AST_DIM: (combinable) [DIM_ALTERNATIVE_SYNTAX]
AST_PROP: []
AST_NULLSAFE_PROP: []
AST_STATIC_PROP: []
AST_CALL: []
AST_CLASS_CONST: []
AST_ASSIGN: []
AST_ASSIGN_REF: []
AST_ASSIGN_OP: [BINARY_BITWISE_OR, BINARY_BITWISE_AND, BINARY_BITWISE_XOR, BINARY_CONCAT, BINARY_ADD, BINARY_SUB, BINARY_MUL, BINARY_DIV, BINARY_MOD, BINARY_POW, BINARY_SHIFT_LEFT, BINARY_SHIFT_RIGHT, BINARY_COALESCE]
AST_BINARY_OP: [BINARY_BITWISE_OR, BINARY_BITWISE_AND, BINARY_BITWISE_XOR, BINARY_CONCAT, BINARY_ADD, BINARY_SUB, BINARY_MUL, BINARY_DIV, BINARY_MOD, BINARY_POW, BINARY_SHIFT_LEFT, BINARY_SHIFT_RIGHT, BINARY_COALESCE, BINARY_BOOL_AND, BINARY_BOOL_OR, BINARY_BOOL_XOR, BINARY_IS_IDENTICAL, BINARY_IS_NOT_IDENTICAL, BINARY_IS_EQUAL, BINARY_IS_NOT_EQUAL, BINARY_IS_SMALLER, BINARY_IS_SMALLER_OR_EQUAL, BINARY_IS_GREATER, BINARY_IS_GREATER_OR_EQUAL, BINARY_SPACESHIP]
AST_ARRAY_ELEM: [ARRAY_ELEM_REF]
AST_NEW: []
AST_INSTANCEOF: []
AST_YIELD: []
AST_STATIC: []
AST_WHILE: []
AST_DO_WHILE: []
AST_IF_ELEM: []
AST_SWITCH: []
AST_SWITCH_CASE: []
AST_DECLARE: []
AST_PROP_ELEM: []
AST_PROP_GROUP: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL]
AST_CONST_ELEM: []
AST_USE_TRAIT: []
AST_TRAIT_PRECEDENCE: []
AST_METHOD_REFERENCE: []
AST_NAMESPACE: []
AST_USE_ELEM: [USE_NORMAL, USE_FUNCTION, USE_CONST]
AST_TRAIT_ALIAS: (combinable) [MODIFIER_PUBLIC, MODIFIER_PROTECTED, MODIFIER_PRIVATE, MODIFIER_STATIC, MODIFIER_ABSTRACT, MODIFIER_FINAL]
AST_GROUP_USE: [USE_NORMAL, USE_FUNCTION, USE_CONST]
AST_ATTRIBUTE: []
AST_MATCH: []
AST_MATCH_ARM: []
AST_NAMED_ARG: []
AST_METHOD_CALL: []
AST_NULLSAFE_METHOD_CALL: []
AST_STATIC_CALL: []
AST_CONDITIONAL: (combinable) [PARENTHESIZED_CONDITIONAL]
AST_TRY: []
AST_CATCH: []
AST_FOR: []
AST_FOREACH: []
AST_ENUM_CASE: []
AST_PARAM: (combinable) [PARAM_REF, PARAM_VARIADIC, PARAM_MODIFIER_PUBLIC, PARAM_MODIFIER_PROTECTED, PARAM_MODIFIER_PRIVATE]
