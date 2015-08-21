#ifndef PHP_AST_H
#define PHP_AST_H

#include "php.h"

extern zend_module_entry ast_module_entry;
#define phpext_ast_ptr &ast_module_entry

#define PHP_AST_VERSION "0.1.0"

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

#define AST_STR_DEFS \
	X(kind) \
	X(name) \
	X(flags) \
	X(lineno) \
	X(children) \
	X(docComment) \
	X(endLineno)

ZEND_BEGIN_MODULE_GLOBALS(ast)
#define X(str) zend_string *str_ ## str;
	AST_STR_DEFS
#undef X
	void *cache_slots[AST_NUM_CACHE_SLOTS];
ZEND_END_MODULE_GLOBALS(ast)

#ifdef ZTS
#define AST_G(v) TSRMG(ast_globals_id, zend_ast_globals *, v)
#else
#define AST_G(v) (ast_globals.v)
#endif

/* Custom ast kind for names */
#define AST_NAME 2048
#define AST_CLOSURE_VAR 2049

extern const size_t ast_kinds_count;
extern const zend_ast_kind ast_kinds[];

const char *ast_kind_to_name(zend_ast_kind kind);
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
