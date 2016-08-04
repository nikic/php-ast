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
#include "zend_smart_str.h"

#define ast_throw_exception(exception_ce, ...) \
	zend_throw_exception_ex(exception_ce, 0, __VA_ARGS__)

#define ast_declare_property(ce, name, value) \
	zend_declare_property_ex((ce), (name), (value), ZEND_ACC_PUBLIC, NULL)

#define ast_register_flag_constant(name, value) \
	REGISTER_NS_LONG_CONSTANT("ast\\flags", name, value, CONST_CS | CONST_PERSISTENT)

#define AST_CACHE_SLOT_KIND     &AST_G(cache_slots)[2 * 0]
#define AST_CACHE_SLOT_FLAGS    &AST_G(cache_slots)[2 * 1]
#define AST_CACHE_SLOT_LINENO   &AST_G(cache_slots)[2 * 2]
#define AST_CACHE_SLOT_CHILDREN &AST_G(cache_slots)[2 * 3]

#define AST_CURRENT_VERSION 35

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

/* Define some constants for PHP 7.0 */
#if PHP_VERSION_ID < 70100
# define IS_VOID 18
# define IS_ITERABLE 19
# define ZEND_TYPE_NULLABLE (1<<8)
# define ZEND_ARRAY_SYNTAX_LIST 1
# define ZEND_ARRAY_SYNTAX_LONG 2
# define ZEND_ARRAY_SYNTAX_SHORT 3
#endif

static inline void ast_update_property(zval *object, zend_string *name, zval *value, void **cache_slot) {
	zval name_zv;
	ZVAL_STR(&name_zv, name);

	Z_TRY_DELREF_P(value);
	Z_OBJ_HT_P(object)->write_property(object, &name_zv, value, cache_slot);
}

ZEND_DECLARE_MODULE_GLOBALS(ast)

static zend_class_entry *ast_node_ce;
static zend_class_entry *ast_decl_ce;

static zend_ast *get_ast(zend_string *code, zend_arena **ast_arena, char *filename) {
	zval code_zv;
	zend_bool original_in_compilation;
	zend_lex_state original_lex_state;
	zend_ast *ast;

	ZVAL_STR_COPY(&code_zv, code);

	original_in_compilation = CG(in_compilation);
	CG(in_compilation) = 1;

	zend_save_lexical_state(&original_lex_state);
	if (zend_prepare_string_for_scanning(&code_zv, filename) == SUCCESS) {
		CG(ast) = NULL;
		CG(ast_arena) = zend_arena_create(1024 * 32);
		LANG_SCNG(yy_state) = yycINITIAL;

		if (zendparse() != 0) {
			zend_ast_destroy(CG(ast));
			zend_arena_destroy(CG(ast_arena));
			CG(ast) = NULL;
		}
	}

	/* restore_lexical_state changes CG(ast) and CG(ast_arena) */
	ast = CG(ast);
	*ast_arena = CG(ast_arena);

	zend_restore_lexical_state(&original_lex_state);
	CG(in_compilation) = original_in_compilation;

	zval_dtor(&code_zv);

	return ast;
}

static inline zend_bool ast_kind_uses_attr(zend_ast_kind kind) {
	return kind == ZEND_AST_PARAM || kind == ZEND_AST_TYPE || kind == ZEND_AST_TRAIT_ALIAS
		|| kind == ZEND_AST_UNARY_OP || kind == ZEND_AST_BINARY_OP || kind == ZEND_AST_ASSIGN_OP
		|| kind == ZEND_AST_CAST || kind == ZEND_AST_MAGIC_CONST || kind == ZEND_AST_ARRAY_ELEM
		|| kind == ZEND_AST_INCLUDE_OR_EVAL || kind == ZEND_AST_USE || kind == ZEND_AST_PROP_DECL
		|| kind == ZEND_AST_GROUP_USE || kind == ZEND_AST_USE_ELEM
		|| kind == AST_NAME || kind == AST_CLOSURE_VAR || kind == ZEND_AST_CLASS_CONST_DECL
		|| kind == ZEND_AST_ARRAY;
}

static inline zend_bool ast_kind_is_decl(zend_ast_kind kind) {
	return kind == ZEND_AST_FUNC_DECL || kind == ZEND_AST_CLOSURE
		|| kind == ZEND_AST_METHOD || kind == ZEND_AST_CLASS;
}

static inline zend_bool ast_is_name(zend_ast *ast, zend_ast *parent, uint32_t i) {
	if (!ast || ast->kind != ZEND_AST_ZVAL || Z_TYPE_P(zend_ast_get_zval(ast)) != IS_STRING) {
		return 0;
	}

	if (parent->kind == ZEND_AST_NAME_LIST) {
		return 1;
	}

	if (i == 0) {
		return parent->kind == ZEND_AST_CATCH || parent->kind == ZEND_AST_CLASS
			|| parent->kind == ZEND_AST_PARAM || parent->kind == ZEND_AST_METHOD_REFERENCE
			|| parent->kind == ZEND_AST_CALL || parent->kind == ZEND_AST_CONST
			|| parent->kind == ZEND_AST_NEW || parent->kind == ZEND_AST_STATIC_CALL
			|| parent->kind == ZEND_AST_CLASS_CONST || parent->kind == ZEND_AST_STATIC_PROP
			;
	}

	if (i == 1) {
		return parent->kind == ZEND_AST_INSTANCEOF;
	}

	if (i == 3) {
		return parent->kind == ZEND_AST_FUNC_DECL || parent->kind == ZEND_AST_CLOSURE
			|| parent->kind == ZEND_AST_METHOD;
	}

	return 0;
}

/* Assumes that ast_is_name is already true */
static inline zend_bool ast_is_type(zend_ast *ast, zend_ast *parent, uint32_t i) {
	if (i == 0) {
		return parent->kind == ZEND_AST_PARAM;
	}
	if (i == 3) {
		return parent->kind == ZEND_AST_CLOSURE || parent->kind == ZEND_AST_FUNC_DECL
			|| parent->kind == ZEND_AST_METHOD;
	}
	return 0;
}

static inline zend_bool ast_is_var_name(zend_ast *ast, zend_ast *parent, uint32_t i) {
	return (parent->kind == ZEND_AST_STATIC && i == 0)
		|| (parent->kind == ZEND_AST_CATCH && i == 1);
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

static inline zend_bool ast_array_is_list(zend_ast *ast) {
	zend_ast_list *list = zend_ast_get_list(ast);
	uint32_t i;
	if (ast->attr != ZEND_ARRAY_SYNTAX_LIST) {
		return 0;
	}

	for (i = 0; i < list->children; i++) {
		if (list->child[i]->child[1] != NULL || list->child[i]->attr) {
			return 0;
		}
	}

	return 1;
}

static inline zend_ast **ast_get_children(zend_ast *ast, uint32_t *count) {
	if (ast_kind_is_decl(ast->kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;
		*count = decl->kind == ZEND_AST_CLASS ? 3 : 4;
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

static void ast_to_zval(zval *zv, zend_ast *ast, zend_long version);

static void ast_create_virtual_node_ex(
		zval *zv, zend_ast_kind kind, zend_ast_attr attr, uint32_t lineno,
		zend_long version, uint32_t num_children, ...) {
	zval tmp_zv;
	va_list va;
	uint32_t i;

	object_init_ex(zv, ast_node_ce);

	ZVAL_LONG(&tmp_zv, kind);
	ast_update_property(zv, AST_STR(str_kind), &tmp_zv, AST_CACHE_SLOT_KIND);

	ZVAL_LONG(&tmp_zv, attr);
	ast_update_property(zv, AST_STR(str_flags), &tmp_zv, AST_CACHE_SLOT_FLAGS);

	ZVAL_LONG(&tmp_zv, lineno);
	ast_update_property(zv, AST_STR(str_lineno), &tmp_zv, AST_CACHE_SLOT_LINENO);

	array_init(&tmp_zv);
	ast_update_property(zv, AST_STR(str_children), &tmp_zv, AST_CACHE_SLOT_CHILDREN);

	va_start(va, num_children);
	for (i = 0; i < num_children; i++) {
		zval *child_zv = va_arg(va, zval *);
		zend_string *child_name = version >= 30 ? ast_kind_child_name(kind, i) : NULL;
		if (child_name) {
			zend_hash_add_new(Z_ARRVAL(tmp_zv), child_name, child_zv);
		} else {
			zend_hash_next_index_insert(Z_ARRVAL(tmp_zv), child_zv);
		}
	}
	va_end(va);
}

static void ast_create_virtual_node(
		zval *zv, zend_ast_kind kind, zend_ast *child, zend_long version) {
	zval child_zv;
	ast_to_zval(&child_zv, child, version);
	return ast_create_virtual_node_ex(
		zv, kind, child->attr, zend_ast_get_lineno(child), version, 1, &child_zv);
}

static void ast_fill_children_ht(HashTable *ht, zend_ast *ast, zend_long version) {
	uint32_t i, count;
	zend_bool is_list = zend_ast_is_list(ast);
	zend_ast **children = ast_get_children(ast, &count);
	for (i = 0; i < count; ++i) {
		zend_ast *child = children[i];
		zend_string *child_name =
			!is_list && version >= 30 ? ast_kind_child_name(ast->kind, i) : NULL;
		zval child_zv;

		if (version >= 20 && ast->kind == ZEND_AST_STMT_LIST) {
			if (child != NULL && child->kind == ZEND_AST_STMT_LIST) {
				ast_fill_children_ht(ht, child, version);
				continue;
			}
			if (version >= 40 && child == NULL) {
				continue;
			}
		}

		/* These two AST_CATCH checks should occur before ast_is_name() */
#if PHP_VERSION_ID >= 70100
		if (ast->kind == ZEND_AST_CATCH && version < 35
				&& i == 0 && zend_ast_get_list(child)->children == 1) {
			/* Emulate PHP 7.0 format (no list) */
			ast_create_virtual_node(
				&child_zv, AST_NAME, zend_ast_get_list(child)->child[0], version);
		}
#else
		if (ast->kind == ZEND_AST_CATCH && version >= 35 && i == 0) {
			/* Emulate PHP 7.1 format (name list) */
			zval tmp;
			ast_create_virtual_node(&tmp, AST_NAME, child, version);
			ast_create_virtual_node_ex(
				&child_zv, ZEND_AST_NAME_LIST, 0, zend_ast_get_lineno(child), version, 1, &tmp);
		}
#endif
		else if (ast_is_name(child, ast, i)) {
			zend_uchar type;
			zend_bool is_nullable = 0;
			if (child->attr & ZEND_TYPE_NULLABLE) {
				is_nullable = 1;
				child->attr &= ~ZEND_TYPE_NULLABLE;
			}

			if (version >= 40 && child->attr == ZEND_NAME_FQ) {
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

			if (version >= 40 && child->attr == ZEND_NAME_NOT_FQ && ast_is_type(child, ast, i)
					&& (type = lookup_builtin_type(zend_ast_get_str(child)))) {
				/* Convert "int" etc typehints to TYPE nodes */
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_TYPE, type, zend_ast_get_lineno(child), version, 0);
			} else {
				ast_create_virtual_node(&child_zv, AST_NAME, child, version);
			}

			if (is_nullable) {
				/* Create explicit AST_NULLABLE_TYPE node */
				zval tmp;
				ZVAL_COPY_VALUE(&tmp, &child_zv);
				ast_create_virtual_node_ex(
					&child_zv, AST_NULLABLE_TYPE, 0, zend_ast_get_lineno(child), version, 1, &tmp);
			}
		} else if (ast->kind == ZEND_AST_CLOSURE_USES) {
			ast_create_virtual_node(&child_zv, AST_CLOSURE_VAR, child, version);
		} else if (version >= 20 && ast_is_var_name(child, ast, i)) {
			ast_create_virtual_node(&child_zv, ZEND_AST_VAR, child, version);
		} else if (version >= 40 && ast_should_normalize_list(child, ast, i)) {
			if (child) {
				zval tmp;
				ast_to_zval(&tmp, child, version);
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_STMT_LIST, 0, zend_ast_get_lineno(child), version, 1, &tmp);
			} else {
				ast_create_virtual_node_ex(
					&child_zv, ZEND_AST_STMT_LIST, 0, zend_ast_get_lineno(ast), version, 0);
			}
		} else if (i == 2
				&& (ast->kind == ZEND_AST_PROP_ELEM || ast->kind == ZEND_AST_CONST_ELEM)) {
			/* Skip docComment child -- It's handled separately */
			continue;
#if PHP_VERSION_ID >= 70100
		} else if (ast->kind == ZEND_AST_LIST) {
			/* Emulate simple variable list */
			ast_to_zval(&child_zv, child->child[0], version);
#else
		} else if (version >= 35 && ast->kind == ZEND_AST_ARRAY
				&& ast->attr == ZEND_ARRAY_SYNTAX_LIST) {
			/* Emulate ARRAY_ELEM list */
			zval ch0, ch1;
			ast_to_zval(&ch0, child, version);
			ZVAL_NULL(&ch1);
			ast_create_virtual_node_ex(
				&child_zv, ZEND_AST_ARRAY_ELEM, 0, zend_ast_get_lineno(child), version,
				2, &ch0, &ch1);
#endif
		} else {
			ast_to_zval(&child_zv, child, version);
		}

		if (child_name) {
			zend_hash_add_new(ht, child_name, &child_zv);
		} else {
			zend_hash_next_index_insert(ht, &child_zv);
		}

	}
}

static void ast_to_zval(zval *zv, zend_ast *ast, zend_long version) {
	zval tmp_zv;
	zend_bool is_decl;

	if (ast == NULL) {
		ZVAL_NULL(zv);
		return;
	}

	if (ast->kind == ZEND_AST_ZVAL) {
		ZVAL_COPY(zv, zend_ast_get_zval(ast));
		return;
	}

	if (version >= 20) {
		switch (ast->kind) {
			case ZEND_AST_ASSIGN_OP:
				ast->attr = ast_assign_op_to_binary_op(ast->attr);
				break;
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
				if (version >= 40) {
					ast->kind = ZEND_AST_BINARY_OP;
					ast->attr = AST_BINARY_COALESCE;
				}
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
		}
	}

#if PHP_VERSION_ID >= 70100
	if (version < 35 && ast->kind == ZEND_AST_ARRAY && ast_array_is_list(ast)) {
		ast->kind = ZEND_AST_LIST;
		ast->attr = 0;
	}
#else
	if (version >= 35 && ast->kind == ZEND_AST_LIST) {
		ast->kind = ZEND_AST_ARRAY;
		ast->attr = ZEND_ARRAY_SYNTAX_LIST;
	}
#endif

	is_decl = ast_kind_is_decl(ast->kind);
	object_init_ex(zv, is_decl ? ast_decl_ce : ast_node_ce);

	ZVAL_LONG(&tmp_zv, ast->kind);
	ast_update_property(zv, AST_STR(str_kind), &tmp_zv, AST_CACHE_SLOT_KIND);

	ZVAL_LONG(&tmp_zv, zend_ast_get_lineno(ast));
	ast_update_property(zv, AST_STR(str_lineno), &tmp_zv, AST_CACHE_SLOT_LINENO);

	if (is_decl) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;

		ZVAL_LONG(&tmp_zv, decl->flags);
		ast_update_property(zv, AST_STR(str_flags), &tmp_zv, NULL);

		ZVAL_LONG(&tmp_zv, decl->end_lineno);
		ast_update_property(zv, AST_STR(str_endLineno), &tmp_zv, NULL);

		if (decl->name) {
			ZVAL_STR_COPY(&tmp_zv, decl->name);
		} else {
			ZVAL_NULL(&tmp_zv);
		}
		ast_update_property(zv, AST_STR(str_name), &tmp_zv, NULL);

		if (decl->doc_comment) {
			ZVAL_STR_COPY(&tmp_zv, decl->doc_comment);
		} else {
			ZVAL_NULL(&tmp_zv);
		}
		ast_update_property(zv, AST_STR(str_docComment), &tmp_zv, NULL);
	} else {
#if PHP_VERSION_ID < 70100
		if (ast->kind == ZEND_AST_CLASS_CONST_DECL) {
			ast->attr = ZEND_ACC_PUBLIC;
		}
#endif
		ZVAL_LONG(&tmp_zv, ast->attr);
		ast_update_property(zv, AST_STR(str_flags), &tmp_zv, AST_CACHE_SLOT_FLAGS);
	}

	/* Convert doc comments on properties and constants into properties */
	if (ast->kind == ZEND_AST_PROP_ELEM && ast->child[2]) {
		ZVAL_STR_COPY(&tmp_zv, zend_ast_get_str(ast->child[2]));
		ast_update_property(zv, AST_STR(str_docComment), &tmp_zv, NULL);
	}
#if PHP_VERSION_ID >= 70100
	if (ast->kind == ZEND_AST_CONST_ELEM && ast->child[2]) {
		ZVAL_STR_COPY(&tmp_zv, zend_ast_get_str(ast->child[2]));
		ast_update_property(zv, AST_STR(str_docComment), &tmp_zv, NULL);
	}
#endif

	array_init(&tmp_zv);
	ast_update_property(zv, AST_STR(str_children), &tmp_zv, AST_CACHE_SLOT_CHILDREN);

	ast_fill_children_ht(Z_ARRVAL(tmp_zv), ast, version);
}

static const zend_long versions[] = {15, 20, 30, 35, 40};
static const size_t versions_count = sizeof(versions)/sizeof(versions[0]);

static zend_string *ast_version_info() {
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
		if (version == 15 || version == 20) {
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
		return;
	}

	ast = get_ast(code, &arena, filename->val);
	if (!ast) {
		zend_string_free(code);
		return;
	}

	ast_to_zval(return_value, ast, version);

	zend_string_free(code);
	zend_ast_destroy(ast);
	zend_arena_destroy(arena);
}

PHP_FUNCTION(parse_code) {
	zend_string *code, *filename = NULL;
	zend_long version = -1;
	zend_ast *ast;
	zend_arena *arena;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "S|lP", &code, &version, &filename) == FAILURE) {
		return;
	}

	if (ast_check_version(version) == FAILURE) {
		return;
	}

	ast = get_ast(code, &arena, filename ? filename->val : "string code");
	if (!ast) {
		return;
	}

	ast_to_zval(return_value, ast, version);

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
		ast_throw_exception(spl_ce_LogicException, "Unknown kind %pd", kind);
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

ZEND_BEGIN_ARG_INFO_EX(arginfo_parse_file, 0, 0, 1)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, version)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_parse_code, 0, 0, 1)
	ZEND_ARG_INFO(0, code)
	ZEND_ARG_INFO(0, version)
	ZEND_ARG_INFO(0, filename)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_get_kind_name, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_kind_uses_flags, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

const zend_function_entry ast_functions[] = {
	ZEND_NS_FE("ast", parse_file, arginfo_parse_file)
	ZEND_NS_FE("ast", parse_code, arginfo_parse_code)
	ZEND_NS_FE("ast", get_kind_name, arginfo_get_kind_name)
	ZEND_NS_FE("ast", kind_uses_flags, arginfo_kind_uses_flags)
	PHP_FE_END
};

PHP_MINFO_FUNCTION(ast) {
	php_info_print_table_start();
	php_info_print_table_header(2, "ast support", "enabled");
	php_info_print_table_end();
}

PHP_RINIT_FUNCTION(ast) {
	memset(AST_G(cache_slots), 0, sizeof(void *) * AST_NUM_CACHE_SLOTS);
	return SUCCESS;
}

PHP_MINIT_FUNCTION(ast) {
	zend_class_entry tmp_ce;

#define X(str) \
	AST_STR(str_ ## str) = zend_new_interned_string( \
		zend_string_init(#str, sizeof(#str) - 1, 1));
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

	ast_register_flag_constant("RETURNS_REF", ZEND_ACC_RETURN_REFERENCE);

	ast_register_flag_constant("CLASS_ABSTRACT", ZEND_ACC_EXPLICIT_ABSTRACT_CLASS);
	ast_register_flag_constant("CLASS_FINAL", ZEND_ACC_FINAL);
	ast_register_flag_constant("CLASS_TRAIT", ZEND_ACC_TRAIT);
	ast_register_flag_constant("CLASS_INTERFACE", ZEND_ACC_INTERFACE);
	ast_register_flag_constant("CLASS_ANONYMOUS", ZEND_ACC_ANON_CLASS);

	ast_register_flag_constant("PARAM_REF", ZEND_PARAM_REF);
	ast_register_flag_constant("PARAM_VARIADIC", ZEND_PARAM_VARIADIC);

	ast_register_flag_constant("TYPE_NULL", IS_NULL);
	ast_register_flag_constant("TYPE_BOOL", _IS_BOOL);
	ast_register_flag_constant("TYPE_LONG", IS_LONG);
	ast_register_flag_constant("TYPE_DOUBLE", IS_DOUBLE);
	ast_register_flag_constant("TYPE_STRING", IS_STRING);
	ast_register_flag_constant("TYPE_ARRAY", IS_ARRAY);
	ast_register_flag_constant("TYPE_OBJECT", IS_OBJECT);
	ast_register_flag_constant("TYPE_CALLABLE", IS_CALLABLE);
	ast_register_flag_constant("TYPE_VOID", IS_VOID);
	ast_register_flag_constant("TYPE_ITERABLE", IS_ITERABLE);

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

	ast_register_flag_constant("ASSIGN_BITWISE_OR", ZEND_ASSIGN_BW_OR);
	ast_register_flag_constant("ASSIGN_BITWISE_AND", ZEND_ASSIGN_BW_AND);
	ast_register_flag_constant("ASSIGN_BITWISE_XOR", ZEND_ASSIGN_BW_XOR);
	ast_register_flag_constant("ASSIGN_CONCAT", ZEND_ASSIGN_CONCAT);
	ast_register_flag_constant("ASSIGN_ADD", ZEND_ASSIGN_ADD);
	ast_register_flag_constant("ASSIGN_SUB", ZEND_ASSIGN_SUB);
	ast_register_flag_constant("ASSIGN_MUL", ZEND_ASSIGN_MUL);
	ast_register_flag_constant("ASSIGN_DIV", ZEND_ASSIGN_DIV);
	ast_register_flag_constant("ASSIGN_MOD", ZEND_ASSIGN_MOD);
	ast_register_flag_constant("ASSIGN_POW", ZEND_ASSIGN_POW);
	ast_register_flag_constant("ASSIGN_SHIFT_LEFT", ZEND_ASSIGN_SL);
	ast_register_flag_constant("ASSIGN_SHIFT_RIGHT", ZEND_ASSIGN_SR);

	ast_register_flag_constant("EXEC_EVAL", ZEND_EVAL);
	ast_register_flag_constant("EXEC_INCLUDE", ZEND_INCLUDE);
	ast_register_flag_constant("EXEC_INCLUDE_ONCE", ZEND_INCLUDE_ONCE);
	ast_register_flag_constant("EXEC_REQUIRE", ZEND_REQUIRE);
	ast_register_flag_constant("EXEC_REQUIRE_ONCE", ZEND_REQUIRE_ONCE);

	ast_register_flag_constant("USE_NORMAL", T_CLASS);
	ast_register_flag_constant("USE_FUNCTION", T_FUNCTION);
	ast_register_flag_constant("USE_CONST", T_CONST);

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

	INIT_CLASS_ENTRY(tmp_ce, "ast\\Node", NULL);
	ast_node_ce = zend_register_internal_class(&tmp_ce);

	{
		zval zv;
		ZVAL_NULL(&zv);

		ast_declare_property(ast_node_ce, AST_STR(str_kind), &zv);
		ast_declare_property(ast_node_ce, AST_STR(str_flags), &zv);
		ast_declare_property(ast_node_ce, AST_STR(str_lineno), &zv);
		ast_declare_property(ast_node_ce, AST_STR(str_children), &zv);
	}

	INIT_CLASS_ENTRY(tmp_ce, "ast\\Node\\Decl", NULL);
	ast_decl_ce = zend_register_internal_class_ex(&tmp_ce, ast_node_ce);

	{
		zval zv;
		ZVAL_NULL(&zv);

		ast_declare_property(ast_decl_ce, AST_STR(str_endLineno), &zv);
		ast_declare_property(ast_decl_ce, AST_STR(str_name), &zv);
		ast_declare_property(ast_decl_ce, AST_STR(str_docComment), &zv);
	}

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(ast) {
#define X(str) zend_string_release(AST_STR(str_ ## str));
	AST_STR_DEFS
#undef X

	return SUCCESS;
}

zend_module_entry ast_module_entry = {
	STANDARD_MODULE_HEADER,
	"ast",
	ast_functions,
	PHP_MINIT(ast),
	PHP_MSHUTDOWN(ast),
	PHP_RINIT(ast),
	NULL,
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
