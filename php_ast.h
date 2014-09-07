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

ZEND_BEGIN_MODULE_GLOBALS(ast)
	zend_string *str_kind;
	zend_string *str_flags;
	zend_string *str_lineno;
	zend_string *str_children;
	zend_string *str_docComment;
	zend_string *str_endLineno;
ZEND_END_MODULE_GLOBALS(ast)

#ifdef ZTS
#define AST_G(v) TSRMG(ast_globals_id, zend_ast_globals *, v)
#else
#define AST_G(v) (ast_globals.v)
#endif

/* Custom ast kind for names */
#define AST_NAME 2048

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
