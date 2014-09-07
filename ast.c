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

ZEND_DECLARE_MODULE_GLOBALS(ast)

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

static zend_bool ast_kind_uses_attr(zend_ast_kind kind) {
	return kind == ZEND_AST_PARAM || kind == ZEND_AST_TYPE || kind == ZEND_AST_TRAIT_ALIAS
		|| kind == ZEND_AST_UNARY_OP || kind == ZEND_AST_BINARY_OP || kind == ZEND_AST_ASSIGN_OP
		|| kind == ZEND_AST_CAST || kind == ZEND_AST_MAGIC_CONST || kind == ZEND_AST_ARRAY_ELEM
		|| kind == ZEND_AST_INCLUDE_OR_EVAL || kind == ZEND_AST_USE || kind == ZEND_AST_PROP_DECL;
}

static zend_bool ast_kind_is_decl(zend_ast_kind kind) {
	return kind == ZEND_AST_FUNC_DECL || kind == ZEND_AST_CLOSURE
		|| kind == ZEND_AST_METHOD || kind == ZEND_AST_CLASS;
}

static zend_bool ast_is_name(zend_ast *ast, zend_ast *parent, uint32_t i) {
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

static zend_ast **ast_get_children(zend_ast *ast, uint32_t *count) {
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

	array_init(zv);

	ZVAL_LONG(&tmp_zv, AST_NAME);
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_kind), &tmp_zv);

	ZVAL_LONG(&tmp_zv, ast->attr);
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_flags), &tmp_zv);

	ZVAL_LONG(&tmp_zv, zend_ast_get_lineno(ast));
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_lineno), &tmp_zv);

	array_init(&tmp_zv);
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_children), &tmp_zv);

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

	array_init(zv);

	ZVAL_LONG(&tmp_zv, ast->kind);
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_kind), &tmp_zv);

	if (ast_kind_uses_attr(ast->kind)) {
		ZVAL_LONG(&tmp_zv, ast->attr);
		zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_flags), &tmp_zv);
	}

	ZVAL_LONG(&tmp_zv, zend_ast_get_lineno(ast));
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_lineno), &tmp_zv);

	if (ast_kind_is_decl(ast->kind)) {
		zend_ast_decl *decl = (zend_ast_decl *) ast;

		ZVAL_LONG(&tmp_zv, decl->flags);
		zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_flags), &tmp_zv);

		ZVAL_LONG(&tmp_zv, decl->end_lineno);
		zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_endLineno), &tmp_zv);

		if (decl->doc_comment) {
			ZVAL_STR(&tmp_zv, zend_string_copy(decl->doc_comment));
		} else {
			ZVAL_NULL(&tmp_zv);
		}
		zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_docComment), &tmp_zv);
	} else if (ast->kind == ZEND_AST_PROP_DECL) {
		zend_ast_list *props = zend_ast_get_list(ast);
		zend_ast *last_prop = props->child[props->children - 1];

		/* PROP_DECL stores the doc comment as last property */
		if (last_prop->kind == ZEND_AST_ZVAL) {
			props->children -= 1;

			ZVAL_STR(&tmp_zv, zend_ast_get_str(last_prop));
			zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_docComment), &tmp_zv);
		}
	}

	array_init(&tmp_zv);
	zend_hash_update(Z_ARRVAL_P(zv), AST_G(str_children), &tmp_zv);

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

PHP_FUNCTION(parseCode) {
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

PHP_FUNCTION(getKindName) {
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

PHP_MINFO_FUNCTION(ast) {
	php_info_print_table_start();
	php_info_print_table_header(2, "ast support", "enabled");
	php_info_print_table_end();
}

PHP_MINIT_FUNCTION(ast) {
	ast_register_kind_constants(INIT_FUNC_ARGS_PASSTHRU);

	return SUCCESS;
}

PHP_RINIT_FUNCTION(ast) {
	AST_G(str_kind) = zend_string_init("kind", sizeof("kind") - 1, 0);
	AST_G(str_flags) = zend_string_init("flags", sizeof("flags") - 1, 0);
	AST_G(str_lineno) = zend_string_init("lineno", sizeof("lineno") - 1, 0);
	AST_G(str_children) = zend_string_init("children", sizeof("children") - 1, 0);
	AST_G(str_docComment) = zend_string_init("docComment", sizeof("docComment") - 1, 0);
	AST_G(str_endLineno) = zend_string_init("endLineno", sizeof("endLineno") - 1, 0);

	AST_G(str_kind) = zend_new_interned_string(AST_G(str_kind) TSRMLS_CC);
	AST_G(str_flags) = zend_new_interned_string(AST_G(str_flags) TSRMLS_CC);
	AST_G(str_lineno) = zend_new_interned_string(AST_G(str_lineno) TSRMLS_CC);
	AST_G(str_children) = zend_new_interned_string(AST_G(str_children) TSRMLS_CC);
	AST_G(str_docComment) = zend_new_interned_string(AST_G(str_docComment) TSRMLS_CC);
	AST_G(str_endLineno) = zend_new_interned_string(AST_G(str_endLineno) TSRMLS_CC);

	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(ast) {
	zend_string_release(AST_G(str_kind));
	zend_string_release(AST_G(str_flags));
	zend_string_release(AST_G(str_lineno));
	zend_string_release(AST_G(str_children));
	zend_string_release(AST_G(str_docComment));
	zend_string_release(AST_G(str_endLineno));

	return SUCCESS;
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_parseCode, 0, 0, 1)
	ZEND_ARG_INFO(0, code)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_getKindName, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

const zend_function_entry ast_functions[] = {
	ZEND_NS_FE("ast", parseCode, arginfo_parseCode)
	ZEND_NS_FE("ast", getKindName, arginfo_getKindName)
	PHP_FE_END
};

zend_module_entry ast_module_entry = {
	STANDARD_MODULE_HEADER,
	"ast",
	ast_functions,
	PHP_MINIT(ast),
	NULL,
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

