#ifndef PHP_AST_H
#define PHP_AST_H

#include "php.h"
#include "ast_str_defs.h"

extern zend_module_entry ast_module_entry;
#define phpext_ast_ptr &ast_module_entry

#define PHP_AST_VERSION "0.1.6dev"

#ifdef PHP_WIN32
#	define PHP_AST_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#	define PHP_AST_API __attribute__ ((visibility("default")))
#else
#	define PHP_AST_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

#define AST_NUM_CACHE_SLOTS (2 * 4)

ZEND_BEGIN_MODULE_GLOBALS(ast)
#define X(str) zend_string *str_ ## str;
	AST_STR_DEFS
#undef X
	void *cache_slots[AST_NUM_CACHE_SLOTS];
ZEND_END_MODULE_GLOBALS(ast)

ZEND_EXTERN_MODULE_GLOBALS(ast)

#define AST_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(ast, v)

#define AST_STR(str) AST_G(str)

/* Custom ast kind for names */
#define AST_NAME          2048
#define AST_CLOSURE_VAR   2049
#define AST_NULLABLE_TYPE 2050

/* Pretend it still exists */
#if PHP_VERSION_ID >= 70100
# define ZEND_AST_LIST ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 1)
#endif

extern const size_t ast_kinds_count;
extern const zend_ast_kind ast_kinds[];

const char *ast_kind_to_name(zend_ast_kind kind);
zend_string *ast_kind_child_name(zend_ast_kind kind, uint32_t child);
void ast_register_kind_constants(INIT_FUNC_ARGS);

#endif	/* PHP_AST_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
