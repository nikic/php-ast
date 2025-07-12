#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_ast.h"
#include "ext/spl/spl_exceptions.h"

#include "zend_language_scanner.h"
#include "zend_language_scanner_defs.h"
#include "zend_language_parser.h"
#include "zend_exceptions.h"
#include "zend_hash.h"
#include "zend_smart_str.h"
#if PHP_VERSION_ID >= 80200
/* Used for AllowDynamicProperties */
#include "zend_attributes.h"
#endif

#if PHP_VERSION_ID < 70200
#error "The php-ast 1.1 release dropped support for php 7.0-7.1. Use php-ast 1.0.16 instead."
#endif

#ifndef ZEND_THIS
#define ZEND_THIS getThis()
#endif

#ifndef ZEND_ARG_INFO_WITH_DEFAULT_VALUE
#define ZEND_ARG_INFO_WITH_DEFAULT_VALUE(pass_by_ref, name, default_value) \
	ZEND_ARG_INFO(pass_by_ref, name)
#endif

#ifndef ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX
#define ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(name, return_reference, required_num_args, class_name, allow_null) \
	ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, class_name, allow_null)
#endif

#ifndef ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE
#define ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(pass_by_ref, name, type_hint, allow_null, default_value) \
	ZEND_ARG_TYPE_INFO(pass_by_ref, name, type_hint, allow_null)
#endif

#include "ast_arginfo.h"

#define ast_throw_exception(exception_ce, ...) \
	zend_throw_exception_ex(exception_ce, 0, __VA_ARGS__)

#define ast_declare_property(ce, name, value) \
	zend_declare_property_ex((ce), (name), (value), ZEND_ACC_PUBLIC, NULL)

#define ast_register_flag_constant(name, value) \
	REGISTER_NS_LONG_CONSTANT("ast\\flags", name, value, CONST_CS | CONST_PERSISTENT)

// These are in the order the properties were declared with ast_declare_property.
#define AST_NODE_PROP_KIND(object) OBJ_PROP_NUM((object), 0)
#define AST_NODE_PROP_FLAGS(object) OBJ_PROP_NUM((object), 1)
#define AST_NODE_PROP_LINENO(object) OBJ_PROP_NUM((object), 2)
#define AST_NODE_PROP_CHILDREN(object) OBJ_PROP_NUM((object), 3)

#define AST_NODE_SET_PROP_KIND(object, num)       ZVAL_LONG(AST_NODE_PROP_KIND((object)), (num))
#define AST_NODE_SET_PROP_FLAGS(object, num)      ZVAL_LONG(AST_NODE_PROP_FLAGS((object)), (num))
#define AST_NODE_SET_PROP_LINENO(object, num)     ZVAL_LONG(AST_NODE_PROP_LINENO((object)), (num))
// Set the ast\Node->children array to the given reference-counted array without incrementing the reference count of the array.
// Do not use this macro with immutable arrays.
#define AST_NODE_SET_PROP_CHILDREN_ARRAY(object, array) ZVAL_ARR(AST_NODE_PROP_CHILDREN((object)), (array))

#define AST_METADATA_PROP_KIND(object) OBJ_PROP_NUM((object), 0)
#define AST_METADATA_PROP_NAME(object) OBJ_PROP_NUM((object), 1)
#define AST_METADATA_PROP_FLAGS(object) OBJ_PROP_NUM((object), 2)
#define AST_METADATA_PROP_FLAGS_COMBINABLE(object) OBJ_PROP_NUM((object), 3)

#define AST_CURRENT_VERSION 110

/* Additional flags for BINARY_OP */
#define AST_BINARY_IS_GREATER 256
#define AST_BINARY_IS_GREATER_OR_EQUAL 257
#define AST_BINARY_BOOL_OR 258
#define AST_BINARY_BOOL_AND 259
#define AST_BINARY_COALESCE 260

/* Flags for UNARY_OP to use instead of AST_SILENCE, AST_UNARY_PLUS, AST_UNARY_MINUS */
#define AST_SILENCE 260
#define AST_PLUS 261
#define AST_MINUS 262

#if PHP_VERSION_ID < 70300
# define ZEND_BIND_REF 1
#endif

#if PHP_VERSION_ID < 70400
# define ZEND_DIM_ALTERNATIVE_SYNTAX (1<<1)
# define ZEND_PARENTHESIZED_CONDITIONAL 1
#endif

/* Make IS_STATIC follow IS_ITERABLE in php 7.0 */
#if PHP_VERSION_ID < 80000
# define IS_STATIC 20
# define IS_MIXED 21
/* In PHP 7.0-7.4, PARAM_REF and PARAM_VARIADIC were 1 and 2. */
# define PARAM_MODIFIER_PUBLIC    (1 << 2)
# define PARAM_MODIFIER_PROTECTED (1 << 3)
# define PARAM_MODIFIER_PRIVATE   (1 << 4)
#else
# define PARAM_MODIFIER_PUBLIC    ZEND_ACC_PUBLIC
# define PARAM_MODIFIER_PROTECTED ZEND_ACC_PROTECTED
# define PARAM_MODIFIER_PRIVATE   ZEND_ACC_PRIVATE
#endif

#if PHP_VERSION_ID < 80100
# define IS_NEVER 22
#endif

#if PHP_VERSION_ID < 80200
# define ZEND_ENCAPS_VAR_DOLLAR_CURLY (1<<0)
# define ZEND_ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR (1<<1)
#endif

#if PHP_VERSION_ID < 80400
# define MODIFIER_PUBLIC_SET    (1 << 11)
# define MODIFIER_PROTECTED_SET (1 << 12)
# define MODIFIER_PRIVATE_SET   (1 << 13)
#else
# define MODIFIER_PUBLIC_SET    ZEND_ACC_PUBLIC_SET
# define MODIFIER_PROTECTED_SET ZEND_ACC_PROTECTED_SET
# define MODIFIER_PRIVATE_SET   ZEND_ACC_PRIVATE_SET
#endif

#if PHP_VERSION_ID >= 80400
# define ZEND_DIM_ALTERNATIVE_SYNTAX (1<<1)
#endif

/* This contains state of the ast Node creator. */
typedef struct ast_state_info {
	zend_long version;
	zend_long declIdCounter;
} ast_state_info_t;

typedef struct _ast_flag_info {
	uint16_t ast_kind;
	zend_bool combinable;
	const char **flags;
} ast_flag_info;

ZEND_DECLARE_MODULE_GLOBALS(ast)

ast_str_globals str_globals;

static zend_class_entry *ast_node_ce;
static zend_class_entry *ast_metadata_ce;

#define AST_FLAG(name) "ast\\flags\\" #name

static const char *name_flags[] = {
	AST_FLAG(NAME_FQ),
	AST_FLAG(NAME_NOT_FQ),
	AST_FLAG(NAME_RELATIVE),
	NULL
};

static const char *class_flags[] = {
	AST_FLAG(CLASS_ABSTRACT),
	AST_FLAG(CLASS_FINAL),
	AST_FLAG(CLASS_TRAIT),
	AST_FLAG(CLASS_INTERFACE),
	AST_FLAG(CLASS_ANONYMOUS),
	AST_FLAG(CLASS_ENUM),
	AST_FLAG(CLASS_READONLY),
	NULL
};

static const char *param_flags[] = {
	AST_FLAG(PARAM_REF),
	AST_FLAG(PARAM_VARIADIC),
	AST_FLAG(PARAM_MODIFIER_PUBLIC),
	AST_FLAG(PARAM_MODIFIER_PROTECTED),
	AST_FLAG(PARAM_MODIFIER_PRIVATE),
	NULL
};

static const char *type_flags[] = {
	AST_FLAG(TYPE_NULL),
	AST_FLAG(TYPE_FALSE),
	AST_FLAG(TYPE_TRUE),
	AST_FLAG(TYPE_BOOL),
	AST_FLAG(TYPE_LONG),
	AST_FLAG(TYPE_DOUBLE),
	AST_FLAG(TYPE_STRING),
	AST_FLAG(TYPE_ARRAY),
	AST_FLAG(TYPE_OBJECT),
	AST_FLAG(TYPE_CALLABLE),
	AST_FLAG(TYPE_VOID),
	AST_FLAG(TYPE_ITERABLE),
	AST_FLAG(TYPE_STATIC),
	AST_FLAG(TYPE_MIXED),
	AST_FLAG(TYPE_NEVER),
	NULL
};

static const char *unary_op_flags[] = {
	AST_FLAG(UNARY_BOOL_NOT),
	AST_FLAG(UNARY_BITWISE_NOT),
	AST_FLAG(UNARY_MINUS),
	AST_FLAG(UNARY_PLUS),
	AST_FLAG(UNARY_SILENCE),
	NULL
};

#define AST_SHARED_BINARY_OP_FLAGS \
	AST_FLAG(BINARY_BITWISE_OR), \
	AST_FLAG(BINARY_BITWISE_AND), \
	AST_FLAG(BINARY_BITWISE_XOR), \
	AST_FLAG(BINARY_CONCAT), \
	AST_FLAG(BINARY_ADD), \
	AST_FLAG(BINARY_SUB), \
	AST_FLAG(BINARY_MUL), \
	AST_FLAG(BINARY_DIV), \
	AST_FLAG(BINARY_MOD), \
	AST_FLAG(BINARY_POW), \
	AST_FLAG(BINARY_SHIFT_LEFT), \
	AST_FLAG(BINARY_SHIFT_RIGHT), \
	AST_FLAG(BINARY_COALESCE) \

static const char *binary_op_flags[] = {
	AST_SHARED_BINARY_OP_FLAGS,
	AST_FLAG(BINARY_BOOL_AND),
	AST_FLAG(BINARY_BOOL_OR),
	AST_FLAG(BINARY_BOOL_XOR),
	AST_FLAG(BINARY_IS_IDENTICAL),
	AST_FLAG(BINARY_IS_NOT_IDENTICAL),
	AST_FLAG(BINARY_IS_EQUAL),
	AST_FLAG(BINARY_IS_NOT_EQUAL),
	AST_FLAG(BINARY_IS_SMALLER),
	AST_FLAG(BINARY_IS_SMALLER_OR_EQUAL),
	AST_FLAG(BINARY_IS_GREATER),
	AST_FLAG(BINARY_IS_GREATER_OR_EQUAL),
	AST_FLAG(BINARY_SPACESHIP),
	NULL
};

static const char *assign_op_flags[] = {
	AST_SHARED_BINARY_OP_FLAGS,
	NULL
};

static const char *magic_const_flags[] = {
	AST_FLAG(MAGIC_LINE),
	AST_FLAG(MAGIC_FILE),
	AST_FLAG(MAGIC_DIR),
	AST_FLAG(MAGIC_NAMESPACE),
	AST_FLAG(MAGIC_FUNCTION),
	AST_FLAG(MAGIC_METHOD),
	AST_FLAG(MAGIC_CLASS),
	AST_FLAG(MAGIC_TRAIT),
	NULL
};

static const char *use_flags[] = {
	AST_FLAG(USE_NORMAL),
	AST_FLAG(USE_FUNCTION),
	AST_FLAG(USE_CONST),
	NULL
};

static const char *include_flags[] = {
	AST_FLAG(EXEC_EVAL),
	AST_FLAG(EXEC_INCLUDE),
	AST_FLAG(EXEC_INCLUDE_ONCE),
	AST_FLAG(EXEC_REQUIRE),
	AST_FLAG(EXEC_REQUIRE_ONCE),
	NULL
};

static const char *array_flags[] = {
	AST_FLAG(ARRAY_SYNTAX_LIST),
	AST_FLAG(ARRAY_SYNTAX_LONG),
	AST_FLAG(ARRAY_SYNTAX_SHORT),
	NULL
};

static const char *array_elem_flags[] = {
	AST_FLAG(ARRAY_ELEM_REF),
	NULL
};

static const char *closure_use_flags[] = {
	AST_FLAG(CLOSURE_USE_REF),
	NULL
};


#define AST_MODIFIER_FLAGS \
	AST_FLAG(MODIFIER_PUBLIC), \
	AST_FLAG(MODIFIER_PROTECTED), \
	AST_FLAG(MODIFIER_PRIVATE), \
	AST_FLAG(MODIFIER_STATIC), \
	AST_FLAG(MODIFIER_ABSTRACT), \
	AST_FLAG(MODIFIER_FINAL), \
	AST_FLAG(MODIFIER_READONLY)

#define AST_ASYMMETRIC_VISIBILITY_FLAGS \
	AST_FLAG(MODIFIER_PUBLIC_SET), \
	AST_FLAG(MODIFIER_PROTECTED_SET), \
	AST_FLAG(MODIFIER_PRIVATE_SET)

static const char *modifier_flags[] = {
	AST_MODIFIER_FLAGS,
	NULL
};

static const char *property_modifier_flags[] = {
	AST_MODIFIER_FLAGS,
	AST_ASYMMETRIC_VISIBILITY_FLAGS,
	NULL
};

static const char *func_flags[] = {
	AST_MODIFIER_FLAGS,
	AST_FLAG(FUNC_RETURNS_REF),
	AST_FLAG(FUNC_GENERATOR),
	NULL
};

static const char *dim_flags[] = {
	AST_FLAG(DIM_ALTERNATIVE_SYNTAX),
	AST_FLAG(ENCAPS_VAR_DOLLAR_CURLY),
	NULL
};

// Flags of AST_CONDITIONAL are marked as combinable in case any other flags get added in the future.
static const char *conditional_flags[] = {
	AST_FLAG(PARENTHESIZED_CONDITIONAL),
	NULL
};

static const char *var_flags[] = {
	AST_FLAG(ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR),
	AST_FLAG(ENCAPS_VAR_DOLLAR_CURLY),
	NULL
};

static const ast_flag_info flag_info[] = {
	{ AST_NAME, 0, name_flags },
	{ ZEND_AST_CLASS, 1, class_flags },
	{ ZEND_AST_PARAM, 1, param_flags },
	{ ZEND_AST_TYPE, 0, type_flags },
	{ ZEND_AST_CAST, 0, type_flags },
	{ ZEND_AST_UNARY_OP, 0, unary_op_flags },
	{ ZEND_AST_BINARY_OP, 0, binary_op_flags },
	{ ZEND_AST_ASSIGN_OP, 0, assign_op_flags },
	{ ZEND_AST_MAGIC_CONST, 0, magic_const_flags },
	{ ZEND_AST_USE, 0, use_flags },
	{ ZEND_AST_GROUP_USE, 0, use_flags },
	{ ZEND_AST_USE_ELEM, 0, use_flags },
	{ ZEND_AST_INCLUDE_OR_EVAL, 0, include_flags },
	{ ZEND_AST_ARRAY, 0, array_flags },
	{ ZEND_AST_ARRAY_ELEM, 0, array_elem_flags },
	{ AST_CLOSURE_VAR, 0, closure_use_flags },
	{ ZEND_AST_METHOD, 1, func_flags },
	{ ZEND_AST_FUNC_DECL, 1, func_flags },
	{ ZEND_AST_CLOSURE, 1, func_flags },
	{ ZEND_AST_ARROW_FUNC, 1, func_flags },
	{ ZEND_AST_PROPERTY_HOOK, 1, func_flags },
	{ ZEND_AST_PROP_DECL, 1, property_modifier_flags },
	{ ZEND_AST_PROP_GROUP, 1, property_modifier_flags },
	{ ZEND_AST_CLASS_CONST_DECL, 1, modifier_flags },
	{ ZEND_AST_CLASS_CONST_GROUP, 1, modifier_flags },
	{ ZEND_AST_TRAIT_ALIAS, 1, modifier_flags },
	{ ZEND_AST_DIM, 1, dim_flags },
	{ ZEND_AST_CONDITIONAL, 1, conditional_flags },
	{ ZEND_AST_VAR, 1, var_flags },
};

static inline void ast_update_property(zval *object, zend_string *name, zval *value) {
#if PHP_VERSION_ID < 80000
	zval name_zv;
	ZVAL_STR(&name_zv, name);
	Z_OBJ_HT_P(object)->write_property(object, &name_zv, value, NULL);
#else
	Z_OBJ_HT_P(object)->write_property(Z_OBJ_P(object), name, value, NULL);
#endif
}

static inline void ast_update_property_long(zval *object, zend_string *name, zend_long value_raw) {
	zval value_zv;
	ZVAL_LONG(&value_zv, value_raw);
	ast_update_property(object, name, &value_zv);
}

static zend_ast *get_ast(zend_string *code, zend_arena **ast_arena, zend_string *filename) {
#if PHP_VERSION_ID >= 80100
	if (filename) {
		return zend_compile_string_to_ast(code, ast_arena, filename);
	} else {
		zend_ast *ast;
		filename = zend_string_init("string code", sizeof("string code") - 1, 0);
		ast = zend_compile_string_to_ast(code, ast_arena, filename);
		zend_string_release_ex(filename, 0);
		return ast;
	}
#else
	zval code_zv;
	zend_bool original_in_compilation;
	zend_lex_state original_lex_state;
	zend_ast *ast;

	ZVAL_STR_COPY(&code_zv, code);

	original_in_compilation = CG(in_compilation);
	CG(in_compilation) = 1;

	zend_save_lexical_state(&original_lex_state);
	zend_prepare_string_for_scanning(&code_zv, filename ? filename->val : "string code");
	CG(ast) = NULL;
	CG(ast_arena) = zend_arena_create(1024 * 32);
	LANG_SCNG(yy_state) = yycINITIAL;

	if (zendparse() != 0) {
		zend_ast_destroy(CG(ast));
		zend_arena_destroy(CG(ast_arena));
		CG(ast) = NULL;
	}

	/* restore_lexical_state changes CG(ast) and CG(ast_arena) */
	ast = CG(ast);
	*ast_arena = CG(ast_arena);

	zend_restore_lexical_state(&original_lex_state);
	CG(in_compilation) = original_in_compilation;

	zval_dtor(&code_zv);

	return ast;
#endif
}

/* Returns whether node->attr (i.e. flags) is used by this node kind. Not to be confused with php 8.0's attributes. */
static inline zend_bool ast_kind_uses_attr(zend_ast_kind kind) {
	return kind == ZEND_AST_PARAM || kind == ZEND_AST_TYPE || kind == ZEND_AST_TRAIT_ALIAS
		|| kind == ZEND_AST_UNARY_OP || kind == ZEND_AST_BINARY_OP || kind == ZEND_AST_ASSIGN_OP
		|| kind == ZEND_AST_CAST || kind == ZEND_AST_MAGIC_CONST || kind == ZEND_AST_ARRAY_ELEM
		|| kind == ZEND_AST_INCLUDE_OR_EVAL || kind == ZEND_AST_USE || kind == ZEND_AST_PROP_DECL
		|| kind == ZEND_AST_PROP_GROUP
		|| kind == ZEND_AST_GROUP_USE || kind == ZEND_AST_USE_ELEM
		|| kind == AST_NAME || kind == AST_CLOSURE_VAR || kind == ZEND_AST_CLASS_CONST_DECL
		|| kind == ZEND_AST_CLASS_CONST_GROUP
		|| kind == ZEND_AST_ARRAY || kind == ZEND_AST_DIM || kind == ZEND_AST_CONDITIONAL
		|| kind == ZEND_AST_VAR;
}

/* Returns true if nodes of this kind are represented with the C struct zend_ast_decl. */
static inline zend_bool ast_kind_is_decl(zend_ast_kind kind) {
	return kind == ZEND_AST_FUNC_DECL || kind == ZEND_AST_CLOSURE
		|| kind == ZEND_AST_ARROW_FUNC
		|| kind == ZEND_AST_PROPERTY_HOOK
		|| kind == ZEND_AST_METHOD || kind == ZEND_AST_CLASS;
}

static inline zend_bool ast_is_name(zend_ast *ast, zend_ast *parent, uint32_t i) {
	if (!ast) {
		return 0;
	}
	if (ast->kind != ZEND_AST_ZVAL || Z_TYPE_P(zend_ast_get_zval(ast)) != IS_STRING) {
		return 0;
	}

	if (parent->kind == ZEND_AST_NAME_LIST) {
		return 1;
	}
#if PHP_VERSION_ID >= 80100
	if (parent->kind == ZEND_AST_TYPE_INTERSECTION) {
		return 1;
	}
#endif
#if PHP_VERSION_ID >= 80000
	if (parent->kind == ZEND_AST_TYPE_UNION) {
		return 1;
	}
#endif

	if (i == 0) {
		return parent->kind == ZEND_AST_CATCH || parent->kind == ZEND_AST_CLASS
			|| parent->kind == ZEND_AST_PARAM || parent->kind == ZEND_AST_METHOD_REFERENCE
			|| parent->kind == ZEND_AST_CALL || parent->kind == ZEND_AST_CONST
			|| parent->kind == ZEND_AST_NEW || parent->kind == ZEND_AST_STATIC_CALL
			|| parent->kind == ZEND_AST_CLASS_CONST || parent->kind == ZEND_AST_STATIC_PROP
			|| parent->kind == ZEND_AST_PROP_GROUP || parent->kind == ZEND_AST_CLASS_NAME
#if PHP_VERSION_ID >= 80000
			|| parent->kind == ZEND_AST_ATTRIBUTE
#endif
			;
	}

#if PHP_VERSION_ID >= 80300
	if (i == 2) {
		return parent->kind == ZEND_AST_CLASS_CONST_GROUP;
	}
#endif

	if (i == 1) {
		return parent->kind == ZEND_AST_INSTANCEOF;
	}

	if (i == 3) {
		return parent->kind == ZEND_AST_FUNC_DECL || parent->kind == ZEND_AST_CLOSURE
#if PHP_VERSION_ID >= 70400
			|| parent->kind == ZEND_AST_ARROW_FUNC
#endif
			|| parent->kind == ZEND_AST_METHOD;
	}

#if PHP_VERSION_ID >= 80100
	if (i == 4) {
		return parent->kind == ZEND_AST_CLASS;
	}
#endif

	return 0;
}

/* Assumes that ast_is_name is already true */
static inline zend_bool ast_is_type(zend_ast *ast, zend_ast *parent, uint32_t i) {
#if PHP_VERSION_ID >= 80100
	if (parent->kind == ZEND_AST_TYPE_INTERSECTION) {
		return 1;
	}
#endif
#if PHP_VERSION_ID >= 80000
	if (parent->kind == ZEND_AST_TYPE_UNION) {
		return 1;
	}
#endif
	if (i == 0) {
		return parent->kind == ZEND_AST_PARAM
#if PHP_VERSION_ID >= 70400
			|| parent->kind == ZEND_AST_PROP_GROUP
#endif
			;
	}
#if PHP_VERSION_ID >= 80300
	if (i == 2) {
		return parent->kind == ZEND_AST_CLASS_CONST_GROUP;
	}
#endif
	if (i == 3) {
		return parent->kind == ZEND_AST_CLOSURE || parent->kind == ZEND_AST_FUNC_DECL
#if PHP_VERSION_ID >= 70400
			|| parent->kind == ZEND_AST_ARROW_FUNC
#endif
			|| parent->kind == ZEND_AST_METHOD;
	}
#if PHP_VERSION_ID >= 80100
	if (i == 4) {
		return parent->kind == ZEND_AST_CLASS;
	}
#endif
	return 0;
}

static inline zend_bool ast_is_var_name(zend_ast *ast, zend_ast *parent, uint32_t i) {
	return (parent->kind == ZEND_AST_STATIC && i == 0)
		|| (parent->kind == ZEND_AST_CATCH && i == 1 && ast != NULL);
}

/* Whether this node may need statement list normalization */
static inline zend_bool ast_should_normalize_list(zend_ast *ast, zend_ast *parent, uint32_t i) {
	if (ast && ast->kind == ZEND_AST_STMT_LIST) {
		return 0;
	}

	if (i == 0) {
		return parent->kind == ZEND_AST_DO_WHILE;
	}
	if (i == 1) {
		if (parent->kind == ZEND_AST_DECLARE) {
			/* declare(); and declare() {} are not the same */
			return ast != NULL;
		}
		return parent->kind == ZEND_AST_IF_ELEM || parent->kind == ZEND_AST_WHILE;
	}
	if (i == 2) {
		return parent->kind == ZEND_AST_CATCH;
	}
	if (i == 3) {
		return parent->kind == ZEND_AST_FOR || parent->kind == ZEND_AST_FOREACH;
	}
	return 0;
}

/* Adopted from zend_compile.c */
typedef struct _builtin_type_info {
	const char* name;
	const size_t name_len;
	const zend_uchar type;
} builtin_type_info;
static const builtin_type_info builtin_types[] = {
	{ZEND_STRL("int"), IS_LONG},
	{ZEND_STRL("float"), IS_DOUBLE},
	{ZEND_STRL("string"), IS_STRING},
	{ZEND_STRL("bool"), _IS_BOOL},
	{ZEND_STRL("void"), IS_VOID},
	{ZEND_STRL("iterable"), IS_ITERABLE},
	{ZEND_STRL("object"), IS_OBJECT},
	{ZEND_STRL("null"), IS_NULL},  /* Null and false for php 8.0 union types */
	{ZEND_STRL("false"), IS_FALSE},
	// {ZEND_STRL("static"), IS_STATIC},  /* Impossible to be parsed before php 8 */
	{ZEND_STRL("mixed"), IS_MIXED},
	{ZEND_STRL("never"), IS_NEVER},
	{ZEND_STRL("true"), IS_TRUE}, /* PHP 8.2 added the true type */
	{NULL, 0, IS_UNDEF}
};
static inline zend_uchar lookup_builtin_type(const zend_string *name) {
	const builtin_type_info *info = &builtin_types[0];
	for (; info->name; ++info) {
		if (ZSTR_LEN(name) == info->name_len
			&& !zend_binary_strcasecmp(ZSTR_VAL(name), ZSTR_LEN(name), info->name, info->name_len)
		) {
			return info->type;
		}
	}
	return 0;
}

#if PHP_VERSION_ID < 70400
static inline zend_ast_attr ast_assign_op_to_binary_op(zend_ast_attr attr) {
	switch (attr) {
		case ZEND_ASSIGN_BW_OR: return ZEND_BW_OR;
		case ZEND_ASSIGN_BW_AND: return ZEND_BW_AND;
		case ZEND_ASSIGN_BW_XOR: return ZEND_BW_XOR;
		case ZEND_ASSIGN_CONCAT: return ZEND_CONCAT;
		case ZEND_ASSIGN_ADD: return ZEND_ADD;
		case ZEND_ASSIGN_SUB: return ZEND_SUB;
		case ZEND_ASSIGN_MUL: return ZEND_MUL;
		case ZEND_ASSIGN_DIV: return ZEND_DIV;
		case ZEND_ASSIGN_MOD: return ZEND_MOD;
		case ZEND_ASSIGN_POW: return ZEND_POW;
		case ZEND_ASSIGN_SL: return ZEND_SL;
		case ZEND_ASSIGN_SR: return ZEND_SR;
		EMPTY_SWITCH_DEFAULT_CASE()
	}
}
#endif

static inline zend_ast **ast_get_children(zend_ast *ast, ast_state_info_t *state, uint32_t *count) {
	if (ast_kind_is_decl(ast->kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;
#if PHP_VERSION_ID >= 80100
		*count = decl->kind == ZEND_AST_CLASS ? (state->version >= 85 ? 5 : 4) : 5;
#elif PHP_VERSION_ID >= 80000
		*count = decl->kind == ZEND_AST_CLASS ? 4 : 5;
#else
		*count = decl->kind == ZEND_AST_CLASS ? 3 : 4;
#endif
		return decl->child;
	} else if (zend_ast_is_list(ast)) {
		zend_ast_list *list = zend_ast_get_list(ast);
		*count = list->children;
		return list->child;
	} else {
		*count = zend_ast_get_num_children(ast);
		return ast->child;
	}
}

static void ast_to_zval(zval *zv, zend_ast *ast, ast_state_info_t *state);

static void ast_create_virtual_node_ex(
		zval *zv, zend_ast_kind kind, zend_ast_attr attr, uint32_t lineno,
		ast_state_info_t *state, uint32_t num_children, ...) {
	va_list va;
	uint32_t i;

	object_init_ex(zv, ast_node_ce);

	zend_object *obj = Z_OBJ_P(zv);

	AST_NODE_SET_PROP_KIND(obj, kind);
	AST_NODE_SET_PROP_FLAGS(obj, attr);
	AST_NODE_SET_PROP_LINENO(obj, lineno);

	array_init_size(AST_NODE_PROP_CHILDREN(obj), num_children);
	HashTable *children = Z_ARRVAL_P(AST_NODE_PROP_CHILDREN(obj));

	va_start(va, num_children);
	for (i = 0; i < num_children; i++) {
		zval *child_zv = va_arg(va, zval *);
		zend_string *child_name = ast_kind_child_name(kind, i);
		if (child_name) {
			zend_hash_add_new(children, child_name, child_zv);
		} else {
			zend_hash_next_index_insert(children, child_zv);
		}
	}
	va_end(va);
}

static void ast_create_virtual_node(
		zval *zv, zend_ast_kind kind, zend_ast_attr attr, zend_ast *child, ast_state_info_t *state) {
	zval child_zv;
	ast_to_zval(&child_zv, child, state);
	ast_create_virtual_node_ex(
		zv, kind, attr, zend_ast_get_lineno(child), state, 1, &child_zv);
}

static inline void ast_name_to_zval(zend_ast *child, zend_ast *ast, zval *child_zv, int i, ast_state_info_t *state) {
	zend_uchar type;
	zend_bool is_nullable = 0;
	if (child->attr & ZEND_TYPE_NULLABLE) {
		is_nullable = 1;
		child->attr &= ~ZEND_TYPE_NULLABLE;
	}

	if (child->attr == ZEND_NAME_FQ) {
		/* Ensure there is no leading \ for fully-qualified names. This can happen if
		 * something like ('\bar')() is used. */
		zval *name = zend_ast_get_zval(child);
		if (Z_STRVAL_P(name)[0] == '\\') {
			zend_string *new_name = zend_string_init(
				Z_STRVAL_P(name) + 1, Z_STRLEN_P(name) - 1, 0);
			zend_string_release(Z_STR_P(name));
			Z_STR_P(name) = new_name;
		}
	}

	if (child->attr == ZEND_NAME_NOT_FQ
			&& ast_is_type(child, ast, i)
			&& (type = lookup_builtin_type(zend_ast_get_str(child)))
			&& (type != IS_MIXED || state->version >= 80)
	) {
		/* Convert "int" etc typehints to TYPE nodes */
		ast_create_virtual_node_ex(
			child_zv, ZEND_AST_TYPE, type, zend_ast_get_lineno(child), state, 0);
	} else {
		ast_create_virtual_node(child_zv, AST_NAME, child->attr, child, state);
	}

	if (is_nullable) {
		/* Create explicit AST_NULLABLE_TYPE node */
		zval tmp;
		ZVAL_COPY_VALUE(&tmp, child_zv);
		ast_create_virtual_node_ex(
			child_zv, AST_NULLABLE_TYPE, 0, zend_ast_get_lineno(child), state, 1, &tmp);
	}
}

static void ast_fill_children_ht(HashTable *ht, zend_ast *ast, ast_state_info_t *state) {
	uint32_t i, count;
	zend_bool is_list = zend_ast_is_list(ast);
	zend_ast **children = ast_get_children(ast, state, &count);
	const zend_ast_kind ast_kind = ast->kind;
	for (i = 0; i < count; ++i) {
		zend_ast *child = children[i];
		zend_string *child_name = !is_list ? ast_kind_child_name(ast_kind, i) : NULL;
		zval child_zv;

		if (ast_kind == ZEND_AST_STMT_LIST) {
			if (child != NULL && child->kind == ZEND_AST_STMT_LIST) {
				ast_fill_children_ht(ht, child, state);
				continue;
			}
			if (child == NULL) {
				continue;
			}
		}

#if PHP_VERSION_ID >= 80000
		if (state->version < 80) {
			switch (ast_kind) {
				case ZEND_AST_PARAM:
					if (i >= 3) {
						/* Skip attributes and doc comment and hooks. */
						continue;
					}
					break;
				case ZEND_AST_METHOD:
				case ZEND_AST_FUNC_DECL:
				case ZEND_AST_CLOSURE:
				case ZEND_AST_ARROW_FUNC:
					if (i == 4) {
						continue;
					}
					break;
				case ZEND_AST_CLASS:
					if (i >= 3) {
						continue;
					}
					break;
				case ZEND_AST_PROP_GROUP:
					if (i == 2) {
						continue;
					}
					break;
			}
		}
#if PHP_VERSION_ID >= 80100
		if (ast_kind == ZEND_AST_CLASS && i == 4) {
			if (state->version < 85)  {
				continue;
			}
		}
#endif
#if PHP_VERSION_ID >= 80300
		if (ast_kind == ZEND_AST_CLASS_CONST_GROUP && i == 2) {
			if (state->version < 100)  {
				continue;
			}
		}
#endif
#if PHP_VERSION_ID >= 80400
		if (ast_kind == ZEND_AST_PROP_ELEM && i == 3) {
			if (state->version < 110) {
				continue;
			}
		}
		if (ast_kind == ZEND_AST_PROPERTY_HOOK && (i == 1 || i == 3)) {
			/* Property hooks don't have uses/returnType but they do have params/stmts/attributes. */
			continue;
		}
		/* Constructor property promotion shorthand can have property hooks. */
		if (ast_kind == ZEND_AST_PARAM && i == 5) {
			if (state->version < 110) {
				continue;
			}
		}
#endif

#endif
		if (ast_is_name(child, ast, i)) {
			ast_name_to_zval(child, ast, &child_zv, i, state);
		} else if (child && child->kind == ZEND_AST_TYPE && (child->attr & ZEND_TYPE_NULLABLE)) {
			child->attr &= ~ZEND_TYPE_NULLABLE;
			ast_create_virtual_node(&child_zv, AST_NULLABLE_TYPE, 0, child, state);
		} else if (ast_kind == ZEND_AST_CLOSURE_USES) {
			ast_create_virtual_node(&child_zv, AST_CLOSURE_VAR, child->attr, child, state);
		} else if (ast_is_var_name(child, ast, i)) {
			ast_create_virtual_node(&child_zv, ZEND_AST_VAR, 0, child, state);
		} else if (ast_should_normalize_list(child, ast, i)) {
			if (child) {
				zval tmp;
				ast_to_zval(&tmp, child, state);
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_STMT_LIST, 0, zend_ast_get_lineno(child), state, 1, &tmp);
			} else {
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_STMT_LIST, 0, zend_ast_get_lineno(ast), state, 0);
			}
		} else if (state->version >= 60 && i == 1
				&& (ast_kind == ZEND_AST_FUNC_DECL || ast_kind == ZEND_AST_METHOD)) {
			/* Skip "uses" child, it is only relevant for closures */
			continue;
#if PHP_VERSION_ID >= 70400
		} else if (i == 1 && ast_kind == ZEND_AST_ARROW_FUNC) {
			/* Skip "uses" child since it is always empty */
			continue;
#endif
		} else if (ast_kind == ZEND_AST_LIST && child != NULL) {
			/* Emulate simple variable list */
			ast_to_zval(&child_zv, child->child[0], state);
		} else {
			ast_to_zval(&child_zv, child, state);
		}
		if (child_name) {
#if PHP_VERSION_ID >= 80200
			if (ast_kind == ZEND_AST_ARROW_FUNC && i == 2) {
				/* Imitate the native arrow function ast structure used in php 7.4-8.1 for stmts */
				/* (This is still different from regular functions, which have AST_STMT_LIST) */
				/* TODO: In a new node type, remove the ZEND_AST_RETURN node instead. */
				zval tmp;
				ZVAL_COPY_VALUE(&tmp, &child_zv);
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_RETURN, 0, zend_ast_get_lineno(child), state, 1, &tmp);
			}
#endif
			zend_hash_add_new(ht, child_name, &child_zv);
		} else {
			zend_hash_next_index_insert(ht, &child_zv);
		}

	}

#if PHP_VERSION_ID < 80000
	if (state->version >= 80) {
		if (ast_kind == ZEND_AST_PARAM) {
			zval tmp;
			ZVAL_NULL(&tmp);
			zend_hash_add_new(ht, AST_STR(str_attributes), &tmp);
			zend_hash_add_new(ht, AST_STR(str_docComment), &tmp);
			return;
		} else if (ast_kind == ZEND_AST_PROP_GROUP) {
			zval tmp;
			ZVAL_NULL(&tmp);
			zend_hash_add_new(ht, AST_STR(str_attributes), &tmp);
			return;
		}
	}
#endif

#if PHP_VERSION_ID < 80400
	if (state->version >= 110) {
		if (ast_kind == ZEND_AST_PARAM || ast_kind == ZEND_AST_PROP_ELEM) {
			zval tmp;
			ZVAL_NULL(&tmp);
			zend_hash_add_new(ht, AST_STR(str_hooks), &tmp);
			return;
		}
	}
#endif

	if (ast_kind_is_decl(ast_kind)) {
		zval id_zval;
#if PHP_VERSION_ID < 80000
		if (state->version >= 80) {
			zval tmp;
			ZVAL_NULL(&tmp);
			zend_hash_add_new(ht, AST_STR(str_attributes), &tmp);
		}
#endif

#if PHP_VERSION_ID < 80100
		if (ast_kind == ZEND_AST_CLASS) {
			if (state->version >= 85) {
				zval tmp;
				ZVAL_NULL(&tmp);
				zend_hash_add_new(ht, AST_STR(str_type), &tmp);
			}
		}
#endif
#if PHP_VERSION_ID < 80300
		if (ast_kind == ZEND_AST_CLASS_CONST_GROUP) {
			if (state->version >= 100) {
				zval tmp;
				ZVAL_NULL(&tmp);
				zend_hash_add_new(ht, AST_STR(str_type), &tmp);
			}
		}
#endif
		ZVAL_LONG(&id_zval, state->declIdCounter);
		state->declIdCounter++;
		zend_hash_add_new(ht, AST_STR(str___declId), &id_zval);
	}
}

static void ast_to_zval(zval *zv, zend_ast *ast, ast_state_info_t *state) {
	zval tmp_zv;

	if (ast == NULL) {
		ZVAL_NULL(zv);
		return;
	}

	if (ast->kind == ZEND_AST_ZVAL) {
		ZVAL_COPY(zv, zend_ast_get_zval(ast));
		return;
	}

	switch (ast->kind) {
#if PHP_VERSION_ID < 70400
		case ZEND_AST_ASSIGN_OP:
			ast->attr = ast_assign_op_to_binary_op(ast->attr);
			break;
#endif
		case ZEND_AST_GREATER:
			ast->kind = ZEND_AST_BINARY_OP;
			ast->attr = AST_BINARY_IS_GREATER;
			break;
		case ZEND_AST_GREATER_EQUAL:
			ast->kind = ZEND_AST_BINARY_OP;
			ast->attr = AST_BINARY_IS_GREATER_OR_EQUAL;
			break;
		case ZEND_AST_OR:
			ast->kind = ZEND_AST_BINARY_OP;
			ast->attr = AST_BINARY_BOOL_OR;
			break;
		case ZEND_AST_AND:
			ast->kind = ZEND_AST_BINARY_OP;
			ast->attr = AST_BINARY_BOOL_AND;
			break;
		case ZEND_AST_COALESCE:
			ast->kind = ZEND_AST_BINARY_OP;
			ast->attr = AST_BINARY_COALESCE;
			break;
		case ZEND_AST_SILENCE:
			ast->kind = ZEND_AST_UNARY_OP;
			ast->attr = AST_SILENCE;
			break;
		case ZEND_AST_UNARY_PLUS:
			ast->kind = ZEND_AST_UNARY_OP;
			ast->attr = AST_PLUS;
			break;
		case ZEND_AST_UNARY_MINUS:
			ast->kind = ZEND_AST_UNARY_OP;
			ast->attr = AST_MINUS;
			break;
#if PHP_VERSION_ID >= 80000
		case ZEND_AST_CLASS_CONST_GROUP:
			// ast->child is [AST_CLASS_CONST_DECL, optional attributes_list]
			if (state->version < 80) {
				// Keep class constants as a list of numerically indexed values in php 8
				ast->child[0]->attr = ast->attr;
				ast_to_zval(zv, ast->child[0], state);
				return;
			}
			break;
#endif
#if PHP_VERSION_ID >= 70400
		case ZEND_AST_PROP_GROUP:
			if (state->version < 70) {
				// In versions less than 70, just omit property type information entirely.
				// ast->child is [type_ast, prop_ast]
				ast_to_zval(zv, ast->child[1], state);
				// The property visibility is on the AST_PROP_GROUP node.
				// Add it to the AST_PROP_DECL node for old
				AST_NODE_SET_PROP_FLAGS(Z_OBJ_P(zv), ast->attr);
				return;
			}
			break;
		case ZEND_AST_ASSIGN_COALESCE:
			ast->kind = ZEND_AST_ASSIGN_OP;
			ast->attr = AST_BINARY_COALESCE;
			break;
		case ZEND_AST_CLASS_NAME:
			if (state->version < 70) {
				zval name_zval;
				ast_to_zval(&name_zval, ast->child[0], state);
				zval class_name_zval;
				ast_create_virtual_node_ex(
						&class_name_zval, AST_NAME, ast->child[0]->attr, zend_ast_get_lineno(ast), state, 1, &name_zval);
				zval const_zval;
				ZVAL_STR_COPY(&const_zval, AST_STR(str_class));
				ast_create_virtual_node_ex(
						zv, ZEND_AST_CLASS_CONST, 0, zend_ast_get_lineno(ast), state, 2, &class_name_zval, &const_zval);
				return;
			}
			break;
#ifdef ZEND_PARENTHESIZED_CONCAT
		case ZEND_AST_BINARY_OP:
			if (ast->attr == ZEND_PARENTHESIZED_CONCAT) {
				ast->attr = ZEND_CONCAT;
			}
			break;
#endif
#else
		case ZEND_AST_CLASS_CONST:
			if (state->version >= 70) {
				// Convert to an AST_CLASS_NAME instead. This is the opposite of the work done in the ZEND_AST_CLASS_NAME case.
				zend_ast *const_name_ast = ast->child[1];
				zend_string *str = zend_ast_get_str(const_name_ast);
				if (zend_string_equals_ci(AST_STR(str_class), str)) {
					zend_ast *class_name_ast = ast->child[0];
					zval class_name_zval;
					if (class_name_ast->kind == ZEND_AST_ZVAL) {
						// e.g. Foo::class
						zval class_name_raw_zval;
						ZVAL_COPY(&class_name_raw_zval, zend_ast_get_zval(class_name_ast));
						ast_create_virtual_node_ex(
								&class_name_zval, AST_NAME, class_name_ast->attr, zend_ast_get_lineno(class_name_ast), state, 1, &class_name_raw_zval);
					} else {
						// e.g. []::class (not a parse error, but a runtime error)
						ast_to_zval(&class_name_zval, class_name_ast, state);
					}

					ast_create_virtual_node_ex(
							zv, ZEND_AST_CLASS_NAME, 0, zend_ast_get_lineno(ast), state, 1, &class_name_zval);
					return;
				}
			}
			break;
#endif
	}

	object_init_ex(zv, ast_node_ce);

	zend_object *obj = Z_OBJ_P(zv);

	zend_ast_kind kind = ast->kind;
	AST_NODE_SET_PROP_KIND(obj, kind);

	AST_NODE_SET_PROP_LINENO(obj, zend_ast_get_lineno(ast));

	array_init(AST_NODE_PROP_CHILDREN(obj));
	HashTable *children = Z_ARRVAL_P(AST_NODE_PROP_CHILDREN(obj));

	if (ast_kind_is_decl(kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;
		uint32_t flags = decl->flags;
#if PHP_VERSION_ID >= 80200
		if (kind == ZEND_AST_CLASS) {
			flags &= ~ZEND_ACC_NO_DYNAMIC_PROPERTIES;
		}
#endif

		AST_NODE_SET_PROP_FLAGS(obj, flags);

		// This is an undeclared dynamic property and has no cache slot.
		ast_update_property_long(zv, AST_STR(str_endLineno), decl->end_lineno);

		if (kind == ZEND_AST_CLOSURE || kind == ZEND_AST_ARROW_FUNC) {
			if (state->version < 110) {
				ZVAL_STR(&tmp_zv, AST_STR(str_bracketed_closure));
			} else {
				/* These never have names. */
				ZVAL_UNDEF(&tmp_zv);
			}
		} else if (decl->name) {
			ZVAL_STR(&tmp_zv, decl->name);
			Z_TRY_ADDREF(tmp_zv);
		} else {
			ZVAL_NULL(&tmp_zv);
		}
		if (!Z_ISUNDEF(tmp_zv)) {
			zend_hash_add_new(children, AST_STR(str_name), &tmp_zv);
		}

		if (decl->doc_comment) {
			ZVAL_STR(&tmp_zv, decl->doc_comment);
			Z_TRY_ADDREF(tmp_zv);
		} else {
			ZVAL_NULL(&tmp_zv);
		}

		zend_hash_add_new(children, AST_STR(str_docComment), &tmp_zv);
	} else {
		AST_NODE_SET_PROP_FLAGS(obj, ast->attr);
	}

	ast_fill_children_ht(children, ast, state);
#if PHP_VERSION_ID < 70400
	if (ast->kind == ZEND_AST_PROP_DECL && state->version >= 70) {
		zval type_zval;
		zval prop_group_zval;
		ZVAL_COPY_VALUE(&prop_group_zval, zv);
		ZVAL_NULL(&type_zval);
		// For version 70, create an AST_PROP_GROUP wrapping the created AST_PROP_DECL.
		if (state->version >= 80) {
			// For version 80, add a null attributes node.
			ast_create_virtual_node_ex(
					zv, ZEND_AST_PROP_GROUP, ast->attr, zend_ast_get_lineno(ast), state, 3, &type_zval, &prop_group_zval, &type_zval);
		} else {
			ast_create_virtual_node_ex(
					zv, ZEND_AST_PROP_GROUP, ast->attr, zend_ast_get_lineno(ast), state, 2, &type_zval, &prop_group_zval);
		}
		AST_NODE_SET_PROP_FLAGS(obj, 0);
	}
#endif
#if PHP_VERSION_ID < 80000
	if (ast->kind == ZEND_AST_CLASS_CONST_DECL && state->version >= 80) {
		zval const_decl_zval;
		zval attributes_zval;
		ZVAL_COPY_VALUE(&const_decl_zval, zv);
		ZVAL_NULL(&attributes_zval);
		AST_NODE_SET_PROP_FLAGS(obj, 0);
		// For version 80, create an AST_CLASS_CONST_GROUP wrapping the created AST_CLASS_CONST_DECL
		ast_create_virtual_node_ex(
				zv, ZEND_AST_CLASS_CONST_GROUP, ast->attr, zend_ast_get_lineno(ast), state, 2, &const_decl_zval, &attributes_zval);
	}
#endif
}

static const zend_long versions[] = {50, 60, 70, 80, 85, 90, 100, 110};
static const size_t versions_count = sizeof(versions)/sizeof(versions[0]);

static inline zend_bool ast_version_deprecated(zend_long version) {
	return version < 70;
}

static zend_string *ast_version_info(void) {
	smart_str str = {0};
	size_t i;

	smart_str_appends(&str, "Current version is ");
	smart_str_append_long(&str, AST_CURRENT_VERSION);
	smart_str_appends(&str, ". All versions (including experimental): {");
	for (i = 0; i < versions_count; ++i) {
		if (i != 0) smart_str_appends(&str, ", ");
		smart_str_append_long(&str, versions[i]);
	}
	smart_str_appends(&str, "}");

	smart_str_0(&str);
	return str.s;
}

static inline zend_bool ast_version_known(zend_long version) {
	size_t i;
	for (i = 0; i < versions_count; ++i) {
		if (version == versions[i]) {
			return 1;
		}
	}
	return 0;
}

static int ast_check_version(zend_long version) {
	zend_string *version_info;

	if (ast_version_known(version)) {
		if (ast_version_deprecated(version)) {
			php_error_docref(NULL, E_DEPRECATED,
				"Version " ZEND_LONG_FMT " is deprecated", version);
		}
		return SUCCESS;
	}

	version_info = ast_version_info();
	if (version != -1) {
		ast_throw_exception(spl_ce_LogicException,
				"Unknown version " ZEND_LONG_FMT ". %s", version, ZSTR_VAL(version_info));
	} else {
		ast_throw_exception(spl_ce_LogicException,
				"No version specified. %s", ZSTR_VAL(version_info));
	}
	zend_string_release(version_info);
	return FAILURE;
}

PHP_FUNCTION(parse_file) {
	zend_string *filename, *code;
	zend_long version = -1;
	ast_state_info_t state;
	zend_ast *ast;
	zend_arena *arena;
	php_stream *stream;
	zend_error_handling error_handling;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "P|l", &filename, &version) == FAILURE) {
		return;
	}

	if (ast_check_version(version) == FAILURE) {
		return;
	}

	zend_replace_error_handling(EH_THROW, spl_ce_RuntimeException, &error_handling);
	stream = php_stream_open_wrapper_ex(filename->val, "rb", REPORT_ERRORS, NULL, NULL);
	if (!stream) {
		zend_restore_error_handling(&error_handling);
		return;
	}

	code = php_stream_copy_to_mem(stream, PHP_STREAM_COPY_ALL, 0);
	php_stream_close(stream);
	zend_restore_error_handling(&error_handling);

	if (!code) {
		/* php_stream_copy_to_mem will return NULL if the file is empty, strangely. */
		code = ZSTR_EMPTY_ALLOC();
	}

	ast = get_ast(code, &arena, filename);
	if (!ast) {
		zend_string_free(code);
		return;
	}

	state.version = version;
	state.declIdCounter = 0;
	ast_to_zval(return_value, ast, &state);

	zend_string_free(code);
	zend_ast_destroy(ast);
	zend_arena_destroy(arena);
}

PHP_FUNCTION(parse_code) {
	zend_string *code, *filename = NULL;
	zend_long version = -1;
	ast_state_info_t state;
	zend_ast *ast;
	zend_arena *arena;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "S|lP", &code, &version, &filename) == FAILURE) {
		return;
	}

	if (ast_check_version(version) == FAILURE) {
		return;
	}

	ast = get_ast(code, &arena, filename);
	if (!ast) {
		return;
	}

	state.version = version;
	state.declIdCounter = 0;
	ast_to_zval(return_value, ast, &state);

	zend_ast_destroy(ast);
	zend_arena_destroy(arena);
}

PHP_FUNCTION(get_kind_name) {
	zend_long kind;
	const char *name;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "l", &kind) == FAILURE) {
		return;
	}

	name = ast_kind_to_name(kind);
	if (!name) {
		ast_throw_exception(spl_ce_LogicException, "Unknown kind " ZEND_LONG_FMT, kind);
		return;
	}

	RETURN_STRING(name);
}

PHP_FUNCTION(kind_uses_flags) {
	zend_long kind;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "l", &kind) == FAILURE) {
		return;
	}

	RETURN_BOOL(ast_kind_uses_attr(kind) || ast_kind_is_decl(kind));
}

static inline const ast_flag_info *ast_get_flag_info(uint16_t ast_kind) {
	size_t i, flag_info_count = sizeof(flag_info)/sizeof(ast_flag_info);
	for (i = 0; i < flag_info_count; i++) {
		const ast_flag_info *info = &flag_info[i];
		if (info->ast_kind == ast_kind) {
			return info;
		}
	}
	return NULL;
}

static void ast_build_metadata(zval *result) {
	size_t i;
	array_init_size(result, ast_kinds_count);
	for (i = 0; i < ast_kinds_count; i++) {
		zend_ast_kind kind = ast_kinds[i];
		const ast_flag_info *flag_info = ast_get_flag_info(kind);
		zval info_zv, tmp_zv;
		zend_object *obj;

		object_init_ex(&info_zv, ast_metadata_ce);
		obj = Z_OBJ(info_zv);

		/* kind */
		ZVAL_LONG(AST_METADATA_PROP_KIND(obj), kind);

		/* name */
		ZVAL_STRING(AST_METADATA_PROP_NAME(obj), ast_kind_to_name(kind));

		/* flags */
		array_init(&tmp_zv);
		if (flag_info) {
			const char **flag;
			for (flag = flag_info->flags; *flag; flag++) {
				add_next_index_string(&tmp_zv, *flag);
			}
		}
		ZVAL_ARR(AST_METADATA_PROP_FLAGS(obj), Z_ARRVAL(tmp_zv));

		/* flagsCombinable */
		ZVAL_BOOL(AST_METADATA_PROP_FLAGS_COMBINABLE(obj), flag_info && flag_info->combinable);

		add_index_zval(result, kind, &info_zv);
	}
}

PHP_FUNCTION(get_metadata) {
	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "") == FAILURE) {
		return;
	}

	if (Z_ISUNDEF(AST_G(metadata))) {
		ast_build_metadata(&AST_G(metadata));
	}

	ZVAL_COPY(return_value, &AST_G(metadata));
}

PHP_FUNCTION(get_supported_versions) {
	zend_bool exclude_deprecated = 0;
	size_t i;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "|b", &exclude_deprecated) == FAILURE) {
		return;
	}

	array_init(return_value);
	for (i = 0; i < versions_count; i++) {
		zend_long version = versions[i];
		if (!exclude_deprecated || !ast_version_deprecated(version)) {
			add_next_index_long(return_value, version);
		}
	}
}

PHP_METHOD(ast_Node, __construct) {
	int num_args = ZEND_NUM_ARGS();
	if (num_args == 0) {
		/* If arguments aren't passed, leave them as their default values. */
		return;
	}

	zend_long kind;
	zend_long flags;
	zval *children;
	zend_long lineno;
	zend_bool kindNull, flagsNull, linenoNull;

	ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 0, 4)
		Z_PARAM_OPTIONAL
		Z_PARAM_LONG_EX(kind, kindNull, 1, 0)
		Z_PARAM_LONG_EX(flags, flagsNull, 1, 0)
		Z_PARAM_ARRAY_EX(children, 1, 0)
		Z_PARAM_LONG_EX(lineno, linenoNull, 1, 0)
	ZEND_PARSE_PARAMETERS_END();

	zend_object *obj = Z_OBJ_P(ZEND_THIS);

	switch (num_args) {
		case 4:
			if (!linenoNull) {
				AST_NODE_SET_PROP_LINENO(obj, lineno);
			}
			/* Falls through - break missing intentionally */
		case 3:
			if (children != NULL) {
				ZVAL_COPY(AST_NODE_PROP_CHILDREN(obj), children);
			}
			/* Falls through - break missing intentionally */
		case 2:
			if (!flagsNull) {
				AST_NODE_SET_PROP_FLAGS(obj, flags);
			}
			/* Falls through - break missing intentionally */
		case 1:
			if (!kindNull) {
				AST_NODE_SET_PROP_KIND(obj, kind);
			}
			/* Falls through - break missing intentionally */
		case 0:
			break;
	}
}

PHP_MINFO_FUNCTION(ast) {
	zend_string *info = ast_version_info();

	php_info_print_table_start();
	php_info_print_table_row(2, "ast support", "enabled");
	php_info_print_table_row(2, "extension version", PHP_AST_VERSION);
	php_info_print_table_row(2, "AST version", ZSTR_VAL(info));
	php_info_print_table_end();

	zend_string_release(info);
}

PHP_RINIT_FUNCTION(ast) {
	ZVAL_UNDEF(&AST_G(metadata));
	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(ast) {
	zval_ptr_dtor(&AST_G(metadata));
	return SUCCESS;
}

PHP_MINIT_FUNCTION(ast) {
	zend_class_entry tmp_ce;
	zval zv_null;
	ZVAL_NULL(&zv_null);

#define X(str, value) \
	AST_STR(str_ ## str) = zend_new_interned_string( \
		zend_string_init(value, sizeof(value) - 1, 1));
	AST_STR_DEFS
#undef X

	ast_register_kind_constants(INIT_FUNC_ARGS_PASSTHRU);

	ast_register_flag_constant("NAME_FQ", ZEND_NAME_FQ);
	ast_register_flag_constant("NAME_NOT_FQ", ZEND_NAME_NOT_FQ);
	ast_register_flag_constant("NAME_RELATIVE", ZEND_NAME_RELATIVE);

	ast_register_flag_constant("MODIFIER_PUBLIC", ZEND_ACC_PUBLIC);
	ast_register_flag_constant("MODIFIER_PROTECTED", ZEND_ACC_PROTECTED);
	ast_register_flag_constant("MODIFIER_PRIVATE", ZEND_ACC_PRIVATE);
	ast_register_flag_constant("MODIFIER_STATIC", ZEND_ACC_STATIC);
	ast_register_flag_constant("MODIFIER_ABSTRACT", ZEND_ACC_ABSTRACT);
	ast_register_flag_constant("MODIFIER_FINAL", ZEND_ACC_FINAL);
	ast_register_flag_constant("MODIFIER_READONLY", ZEND_ACC_READONLY);
	ast_register_flag_constant("MODIFIER_PUBLIC_SET", MODIFIER_PUBLIC_SET);
	ast_register_flag_constant("MODIFIER_PROTECTED_SET", MODIFIER_PROTECTED_SET);
	ast_register_flag_constant("MODIFIER_PRIVATE_SET", MODIFIER_PRIVATE_SET);

	ast_register_flag_constant("PARAM_MODIFIER_PUBLIC", PARAM_MODIFIER_PUBLIC);
	ast_register_flag_constant("PARAM_MODIFIER_PROTECTED", PARAM_MODIFIER_PROTECTED);
	ast_register_flag_constant("PARAM_MODIFIER_PRIVATE", PARAM_MODIFIER_PRIVATE);

	ast_register_flag_constant("RETURNS_REF", ZEND_ACC_RETURN_REFERENCE);
	ast_register_flag_constant("FUNC_RETURNS_REF", ZEND_ACC_RETURN_REFERENCE);
	ast_register_flag_constant("FUNC_GENERATOR", ZEND_ACC_GENERATOR);

	ast_register_flag_constant("ARRAY_ELEM_REF", 1);
	ast_register_flag_constant("CLOSURE_USE_REF", ZEND_BIND_REF);

	ast_register_flag_constant("CLASS_ABSTRACT", ZEND_ACC_EXPLICIT_ABSTRACT_CLASS);
	ast_register_flag_constant("CLASS_FINAL", ZEND_ACC_FINAL);
	ast_register_flag_constant("CLASS_TRAIT", ZEND_ACC_TRAIT);
	ast_register_flag_constant("CLASS_INTERFACE", ZEND_ACC_INTERFACE);
	ast_register_flag_constant("CLASS_ANONYMOUS", ZEND_ACC_ANON_CLASS);
	ast_register_flag_constant("CLASS_ENUM", ZEND_ACC_ENUM);
	ast_register_flag_constant("CLASS_READONLY", ZEND_ACC_READONLY_CLASS);

	ast_register_flag_constant("PARAM_REF", ZEND_PARAM_REF);
	ast_register_flag_constant("PARAM_VARIADIC", ZEND_PARAM_VARIADIC);

	ast_register_flag_constant("TYPE_NULL", IS_NULL);
	ast_register_flag_constant("TYPE_FALSE", IS_FALSE);
	ast_register_flag_constant("TYPE_TRUE", IS_TRUE);
	ast_register_flag_constant("TYPE_BOOL", _IS_BOOL);
	ast_register_flag_constant("TYPE_LONG", IS_LONG);
	ast_register_flag_constant("TYPE_DOUBLE", IS_DOUBLE);
	ast_register_flag_constant("TYPE_STRING", IS_STRING);
	ast_register_flag_constant("TYPE_ARRAY", IS_ARRAY);
	ast_register_flag_constant("TYPE_OBJECT", IS_OBJECT);
	ast_register_flag_constant("TYPE_CALLABLE", IS_CALLABLE);
	ast_register_flag_constant("TYPE_VOID", IS_VOID);
	ast_register_flag_constant("TYPE_ITERABLE", IS_ITERABLE);
	ast_register_flag_constant("TYPE_STATIC", IS_STATIC);
	ast_register_flag_constant("TYPE_MIXED", IS_MIXED);
	ast_register_flag_constant("TYPE_NEVER", IS_NEVER);

	ast_register_flag_constant("UNARY_BOOL_NOT", ZEND_BOOL_NOT);
	ast_register_flag_constant("UNARY_BITWISE_NOT", ZEND_BW_NOT);
	ast_register_flag_constant("UNARY_SILENCE", AST_SILENCE);
	ast_register_flag_constant("UNARY_PLUS", AST_PLUS);
	ast_register_flag_constant("UNARY_MINUS", AST_MINUS);

	ast_register_flag_constant("BINARY_BOOL_AND", AST_BINARY_BOOL_AND);
	ast_register_flag_constant("BINARY_BOOL_OR", AST_BINARY_BOOL_OR);
	ast_register_flag_constant("BINARY_BOOL_XOR", ZEND_BOOL_XOR);
	ast_register_flag_constant("BINARY_BITWISE_OR", ZEND_BW_OR);
	ast_register_flag_constant("BINARY_BITWISE_AND", ZEND_BW_AND);
	ast_register_flag_constant("BINARY_BITWISE_XOR", ZEND_BW_XOR);
	ast_register_flag_constant("BINARY_CONCAT", ZEND_CONCAT);
	ast_register_flag_constant("BINARY_ADD", ZEND_ADD);
	ast_register_flag_constant("BINARY_SUB", ZEND_SUB);
	ast_register_flag_constant("BINARY_MUL", ZEND_MUL);
	ast_register_flag_constant("BINARY_DIV", ZEND_DIV);
	ast_register_flag_constant("BINARY_MOD", ZEND_MOD);
	ast_register_flag_constant("BINARY_POW", ZEND_POW);
	ast_register_flag_constant("BINARY_SHIFT_LEFT", ZEND_SL);
	ast_register_flag_constant("BINARY_SHIFT_RIGHT", ZEND_SR);
	ast_register_flag_constant("BINARY_IS_IDENTICAL", ZEND_IS_IDENTICAL);
	ast_register_flag_constant("BINARY_IS_NOT_IDENTICAL", ZEND_IS_NOT_IDENTICAL);
	ast_register_flag_constant("BINARY_IS_EQUAL", ZEND_IS_EQUAL);
	ast_register_flag_constant("BINARY_IS_NOT_EQUAL", ZEND_IS_NOT_EQUAL);
	ast_register_flag_constant("BINARY_IS_SMALLER", ZEND_IS_SMALLER);
	ast_register_flag_constant("BINARY_IS_SMALLER_OR_EQUAL", ZEND_IS_SMALLER_OR_EQUAL);
	ast_register_flag_constant("BINARY_IS_GREATER", AST_BINARY_IS_GREATER);
	ast_register_flag_constant("BINARY_IS_GREATER_OR_EQUAL", AST_BINARY_IS_GREATER_OR_EQUAL);
	ast_register_flag_constant("BINARY_SPACESHIP", ZEND_SPACESHIP);
	ast_register_flag_constant("BINARY_COALESCE", AST_BINARY_COALESCE);

	ast_register_flag_constant("EXEC_EVAL", ZEND_EVAL);
	ast_register_flag_constant("EXEC_INCLUDE", ZEND_INCLUDE);
	ast_register_flag_constant("EXEC_INCLUDE_ONCE", ZEND_INCLUDE_ONCE);
	ast_register_flag_constant("EXEC_REQUIRE", ZEND_REQUIRE);
	ast_register_flag_constant("EXEC_REQUIRE_ONCE", ZEND_REQUIRE_ONCE);

	ast_register_flag_constant("USE_NORMAL", ZEND_SYMBOL_CLASS);
	ast_register_flag_constant("USE_FUNCTION", ZEND_SYMBOL_FUNCTION);
	ast_register_flag_constant("USE_CONST", ZEND_SYMBOL_CONST);

	ast_register_flag_constant("MAGIC_LINE", T_LINE);
	ast_register_flag_constant("MAGIC_FILE", T_FILE);
	ast_register_flag_constant("MAGIC_DIR", T_DIR);
	ast_register_flag_constant("MAGIC_NAMESPACE", T_NS_C);
	ast_register_flag_constant("MAGIC_FUNCTION", T_FUNC_C);
	ast_register_flag_constant("MAGIC_METHOD", T_METHOD_C);
	ast_register_flag_constant("MAGIC_CLASS", T_CLASS_C);
	ast_register_flag_constant("MAGIC_TRAIT", T_TRAIT_C);

	ast_register_flag_constant("ARRAY_SYNTAX_LIST", ZEND_ARRAY_SYNTAX_LIST);
	ast_register_flag_constant("ARRAY_SYNTAX_LONG", ZEND_ARRAY_SYNTAX_LONG);
	ast_register_flag_constant("ARRAY_SYNTAX_SHORT", ZEND_ARRAY_SYNTAX_SHORT);

	ast_register_flag_constant("DIM_ALTERNATIVE_SYNTAX", ZEND_DIM_ALTERNATIVE_SYNTAX);

	ast_register_flag_constant("PARENTHESIZED_CONDITIONAL", ZEND_PARENTHESIZED_CONDITIONAL);

	ast_register_flag_constant("ENCAPS_VAR_DOLLAR_CURLY", ZEND_ENCAPS_VAR_DOLLAR_CURLY);
	ast_register_flag_constant("ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR", ZEND_ENCAPS_VAR_DOLLAR_CURLY_VAR_VAR);

	INIT_CLASS_ENTRY(tmp_ce, "ast\\Node", class_ast_Node_methods);
	ast_node_ce = zend_register_internal_class(&tmp_ce);
	ast_declare_property(ast_node_ce, AST_STR(str_kind), &zv_null);
	ast_declare_property(ast_node_ce, AST_STR(str_flags), &zv_null);
	ast_declare_property(ast_node_ce, AST_STR(str_lineno), &zv_null);
	ast_declare_property(ast_node_ce, AST_STR(str_children), &zv_null);
#ifdef ZEND_ACC_ALLOW_DYNAMIC_PROPERTIES
	zend_add_class_attribute(ast_node_ce, zend_ce_allow_dynamic_properties->name, 0);
	ast_node_ce->ce_flags |= ZEND_ACC_ALLOW_DYNAMIC_PROPERTIES;
#endif

	INIT_CLASS_ENTRY(tmp_ce, "ast\\Metadata", NULL);
	ast_metadata_ce = zend_register_internal_class(&tmp_ce);
	ast_declare_property(ast_metadata_ce, AST_STR(str_kind), &zv_null);
	ast_declare_property(ast_metadata_ce, AST_STR(str_name), &zv_null);
	ast_declare_property(ast_metadata_ce, AST_STR(str_flags), &zv_null);
	ast_declare_property(ast_metadata_ce, AST_STR(str_flagsCombinable), &zv_null);

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(ast) {
#define X(str, value) zend_string_release(AST_STR(str_ ## str));
	AST_STR_DEFS
#undef X

	return SUCCESS;
}

zend_module_entry ast_module_entry = {
	STANDARD_MODULE_HEADER,
	"ast",
	ext_functions,
	PHP_MINIT(ast),
	PHP_MSHUTDOWN(ast),
	PHP_RINIT(ast),
	PHP_RSHUTDOWN(ast),
	PHP_MINFO(ast),
	PHP_AST_VERSION,
	PHP_MODULE_GLOBALS(ast),
	NULL,
	NULL,
	NULL,
	STANDARD_MODULE_PROPERTIES_EX
};

#ifdef COMPILE_DL_AST
ZEND_GET_MODULE(ast)
#endif
