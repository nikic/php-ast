#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_ast.h"

#include "zend_language_scanner.h"
#include "zend_language_scanner_defs.h"
#include "zend_exceptions.h"

#define ast_throw_exception(...) \
	zend_throw_exception_ex(zend_exception_get_default(TSRMLS_C), 0 TSRMLS_CC, __VA_ARGS__)

#define ast_init_string_global(str) \
	AST_G(str_ ## str) = zend_new_interned_string( \
		zend_string_init(#str, sizeof(#str) - 1, 1) TSRMLS_CC)

#define ast_declare_property(ce, name, value) \
	zend_declare_property_ex((ce), (name), (value), ZEND_ACC_PUBLIC, NULL TSRMLS_CC)

#define ast_register_flag_constant(name, value) \
	REGISTER_NS_LONG_CONSTANT("ast\\flags", name, value, CONST_CS | CONST_PERSISTENT)

#define AST_CACHE_SLOT_KIND     &AST_G(cache_slots)[2 * 0]
#define AST_CACHE_SLOT_FLAGS    &AST_G(cache_slots)[2 * 1]
#define AST_CACHE_SLOT_LINENO   &AST_G(cache_slots)[2 * 2]
#define AST_CACHE_SLOT_CHILDREN &AST_G(cache_slots)[2 * 3]

static inline void ast_update_property(zval *object, zend_string *name, zval *value, void **cache_slot TSRMLS_DC) {
	zval name_zv;
	ZVAL_STR(&name_zv, name);

	Z_TRY_DELREF_P(value);
	Z_OBJ_HT_P(object)->write_property(object, &name_zv, value, cache_slot TSRMLS_CC);
}

ZEND_DECLARE_MODULE_GLOBALS(ast)

static zend_class_entry *ast_node_ce;

static zend_ast *get_ast(zend_string *code TSRMLS_DC) {
	zval code_zv;
	zend_bool original_in_compilation;
	zend_lex_state original_lex_state;

	ZVAL_STR(&code_zv, zend_string_copy(code));

	original_in_compilation = CG(in_compilation);
	CG(in_compilation) = 1;

	zend_save_lexical_state(&original_lex_state TSRMLS_CC);
	if (zend_prepare_string_for_scanning(&code_zv, "string code" TSRMLS_CC) == SUCCESS) {
		CG(ast) = NULL;
		CG(ast_arena) = zend_arena_create(1024 * 32);
		LANG_SCNG(yy_state) = yycINITIAL;

		if (zendparse(TSRMLS_C) != 0) {
			zend_ast_destroy(CG(ast));
			zend_arena_destroy(CG(ast_arena));
			CG(ast) = NULL;
		}
	}

	zend_restore_lexical_state(&original_lex_state TSRMLS_CC);
	CG(in_compilation) = original_in_compilation;

	zval_dtor(&code_zv);

	return CG(ast);
}

static inline zend_bool ast_kind_uses_attr(zend_ast_kind kind) {
	return kind == ZEND_AST_PARAM || kind == ZEND_AST_TYPE || kind == ZEND_AST_TRAIT_ALIAS
		|| kind == ZEND_AST_UNARY_OP || kind == ZEND_AST_BINARY_OP || kind == ZEND_AST_ASSIGN_OP
		|| kind == ZEND_AST_CAST || kind == ZEND_AST_MAGIC_CONST || kind == ZEND_AST_ARRAY_ELEM
		|| kind == ZEND_AST_INCLUDE_OR_EVAL || kind == ZEND_AST_USE || kind == ZEND_AST_PROP_DECL
		|| kind == AST_NAME;
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
			|| parent->kind == ZEND_AST_RESOLVE_CLASS_NAME;
	}

	if (i == 1) {
		return parent->kind == ZEND_AST_INSTANCEOF;
	}

	return 0;
}

static inline zend_ast **ast_get_children(zend_ast *ast, uint32_t *count) {
	if (ast_kind_is_decl(ast->kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;
		*count = 3;
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

static void ast_name_to_zval(zval *zv, zend_ast *ast TSRMLS_DC) {
	zval tmp_zv, tmp_zv2;

	object_init_ex(zv, ast_node_ce);

	ZVAL_LONG(&tmp_zv, AST_NAME);
	ast_update_property(zv, AST_G(str_kind), &tmp_zv, AST_CACHE_SLOT_KIND TSRMLS_CC);

	ZVAL_LONG(&tmp_zv, ast->attr);
	ast_update_property(zv, AST_G(str_flags), &tmp_zv, AST_CACHE_SLOT_FLAGS TSRMLS_CC);

	ZVAL_LONG(&tmp_zv, zend_ast_get_lineno(ast));
	ast_update_property(zv, AST_G(str_lineno), &tmp_zv, AST_CACHE_SLOT_LINENO TSRMLS_CC);

	array_init(&tmp_zv);
	ast_update_property(zv, AST_G(str_children), &tmp_zv, AST_CACHE_SLOT_CHILDREN TSRMLS_CC);

	ZVAL_COPY(&tmp_zv2, zend_ast_get_zval(ast));
	zend_hash_next_index_insert(Z_ARRVAL(tmp_zv), &tmp_zv2);
}

static void ast_to_zval(zval *zv, zend_ast *ast TSRMLS_DC) {
	zval tmp_zv;

	if (ast == NULL) {
		ZVAL_NULL(zv);
		return;
	}

	if (ast->kind == ZEND_AST_ZVAL) {
		ZVAL_COPY(zv, zend_ast_get_zval(ast));
		return;
	}

	object_init_ex(zv, ast_node_ce);

	ZVAL_LONG(&tmp_zv, ast->kind);
	ast_update_property(zv, AST_G(str_kind), &tmp_zv, AST_CACHE_SLOT_KIND TSRMLS_CC);

	ZVAL_LONG(&tmp_zv, zend_ast_get_lineno(ast));
	ast_update_property(zv, AST_G(str_lineno), &tmp_zv, AST_CACHE_SLOT_LINENO TSRMLS_CC);

	if (ast_kind_is_decl(ast->kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;

		ZVAL_LONG(&tmp_zv, decl->flags);
		ast_update_property(zv, AST_G(str_flags), &tmp_zv, AST_CACHE_SLOT_FLAGS TSRMLS_CC);

		ZVAL_LONG(&tmp_zv, decl->end_lineno);
		ast_update_property(zv, AST_G(str_endLineno), &tmp_zv, NULL TSRMLS_CC);

		if (decl->doc_comment) {
			ZVAL_STR(&tmp_zv, zend_string_copy(decl->doc_comment));
		} else {
			ZVAL_NULL(&tmp_zv);
		}
		ast_update_property(zv, AST_G(str_docComment), &tmp_zv, NULL TSRMLS_CC);
	} else {
		ZVAL_LONG(&tmp_zv, ast->attr);
		ast_update_property(zv, AST_G(str_flags), &tmp_zv, AST_CACHE_SLOT_FLAGS TSRMLS_CC);
	}
	
	if (ast->kind == ZEND_AST_PROP_DECL) {
		zend_ast_list *props = zend_ast_get_list(ast);
		zend_ast *last_prop = props->child[props->children - 1];

		/* PROP_DECL stores the doc comment as last property */
		if (last_prop->kind == ZEND_AST_ZVAL) {
			props->children -= 1;

			ZVAL_STR(&tmp_zv, zend_ast_get_str(last_prop));
			ast_update_property(zv, AST_G(str_docComment), &tmp_zv, NULL TSRMLS_CC);
		}
	}

	array_init(&tmp_zv);
	ast_update_property(zv, AST_G(str_children), &tmp_zv, AST_CACHE_SLOT_CHILDREN TSRMLS_CC);

	{
		uint32_t i, count;
		zend_ast **children = ast_get_children(ast, &count);
		for (i = 0; i < count; ++i) {
			zend_ast *child = children[i];
			zval child_zv;

			if (ast_is_name(child, ast, i)) {
				ast_name_to_zval(&child_zv, child TSRMLS_CC);
			} else {
				ast_to_zval(&child_zv, child TSRMLS_CC);
			}

			zend_hash_next_index_insert(Z_ARRVAL(tmp_zv), &child_zv);
		}
	}
}

PHP_FUNCTION(parse_code) {
	zend_string *code;
	zend_ast *ast;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "S", &code) == FAILURE) {
		return;
	}

	ast = get_ast(code TSRMLS_CC);
	if (!ast) {
		RETURN_FALSE;
	}

	ast_to_zval(return_value, ast TSRMLS_CC);

	zend_ast_destroy(ast);
	zend_arena_destroy(CG(ast_arena));
}

PHP_FUNCTION(get_kind_name) {
	zend_long kind;
	const char *name;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &kind) == FAILURE) {
		return;
	}

	name = ast_kind_to_name(kind);
	if (!name) {
		ast_throw_exception("Unknown kind %pd", kind);
		return;
	}

	RETURN_STRING(name);
}

PHP_FUNCTION(kind_uses_flags) {
	zend_long kind;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &kind) == FAILURE) {
		return;
	}

	RETURN_BOOL(ast_kind_uses_attr(kind) || ast_kind_is_decl(kind));
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_parse_code, 0, 0, 1)
	ZEND_ARG_INFO(0, code)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_get_kind_name, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_kind_uses_flags, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

const zend_function_entry ast_functions[] = {
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

	ast_init_string_global(kind);
	ast_init_string_global(flags);
	ast_init_string_global(lineno);
	ast_init_string_global(children);
	ast_init_string_global(endLineno);
	ast_init_string_global(docComment);

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
	ast_register_flag_constant("CLASS_FINAL", ZEND_ACC_FINAL_CLASS);
	ast_register_flag_constant("CLASS_TRAIT", ZEND_ACC_TRAIT);
	ast_register_flag_constant("CLASS_INTERFACE", ZEND_ACC_INTERFACE);

	ast_register_flag_constant("PARAM_REF", ZEND_PARAM_REF);
	ast_register_flag_constant("PARAM_VARIADIC", ZEND_PARAM_VARIADIC);

	ast_register_flag_constant("TYPE_NULL", IS_NULL);
	ast_register_flag_constant("TYPE_BOOL", _IS_BOOL);
	ast_register_flag_constant("TYPE_LONG", IS_LONG);
	ast_register_flag_constant("TYPE_DOUBLE", IS_DOUBLE);
	ast_register_flag_constant("TYPE_STRING", IS_STRING);
	ast_register_flag_constant("TYPE_ARRAY", IS_ARRAY);
	ast_register_flag_constant("TYPE_OBJECT", IS_OBJECT);

	ast_register_flag_constant("UNARY_BOOL_NOT", ZEND_BOOL_NOT);
	ast_register_flag_constant("UNARY_BITWISE_NOT", ZEND_BW_NOT);

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
	ast_register_flag_constant("BINARY_SHIFT_LEFT", ZEND_SL);
	ast_register_flag_constant("BINARY_SHIFT_RIGHT", ZEND_SR);
	ast_register_flag_constant("BINARY_IS_IDENTICAL", ZEND_IS_IDENTICAL);
	ast_register_flag_constant("BINARY_IS_NOT_IDENTICAL", ZEND_IS_NOT_IDENTICAL);
	ast_register_flag_constant("BINARY_IS_EQUAL", ZEND_IS_EQUAL);
	ast_register_flag_constant("BINARY_IS_NOT_EQUAL", ZEND_IS_NOT_EQUAL);
	ast_register_flag_constant("BINARY_IS_SMALLER", ZEND_IS_SMALLER);
	ast_register_flag_constant("BINARY_IS_SMALLER_OR_EQUAL", ZEND_IS_SMALLER_OR_EQUAL);

	ast_register_flag_constant("ASSIGN_BITWISE_OR", ZEND_ASSIGN_BW_OR);
	ast_register_flag_constant("ASSIGN_BITWISE_AND", ZEND_ASSIGN_BW_AND);
	ast_register_flag_constant("ASSIGN_BITWISE_XOR", ZEND_ASSIGN_BW_XOR);
	ast_register_flag_constant("ASSIGN_CONCAT", ZEND_ASSIGN_CONCAT);
	ast_register_flag_constant("ASSIGN_ADD", ZEND_ASSIGN_ADD);
	ast_register_flag_constant("ASSIGN_SUB", ZEND_ASSIGN_SUB);
	ast_register_flag_constant("ASSIGN_MUL", ZEND_ASSIGN_MUL);
	ast_register_flag_constant("ASSIGN_DIV", ZEND_ASSIGN_DIV);
	ast_register_flag_constant("ASSIGN_MOD", ZEND_ASSIGN_MOD);
	ast_register_flag_constant("ASSIGN_SHIFT_LEFT", ZEND_ASSIGN_SL);
	ast_register_flag_constant("ASSIGN_SHIFT_RIGHT", ZEND_ASSIGN_SR);

	INIT_CLASS_ENTRY(tmp_ce, "ast\\Node", NULL);
	ast_node_ce = zend_register_internal_class(&tmp_ce TSRMLS_CC);

	{
		zval zv;
		ZVAL_NULL(&zv);

		ast_declare_property(ast_node_ce, AST_G(str_kind), &zv);
		ast_declare_property(ast_node_ce, AST_G(str_flags), &zv);
		ast_declare_property(ast_node_ce, AST_G(str_lineno), &zv);
		ast_declare_property(ast_node_ce, AST_G(str_children), &zv);
	}

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(ast) {
	zend_string_release(AST_G(str_kind));
	zend_string_release(AST_G(str_flags));
	zend_string_release(AST_G(str_lineno));
	zend_string_release(AST_G(str_children));
	zend_string_release(AST_G(str_docComment));
	zend_string_release(AST_G(str_endLineno));

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

