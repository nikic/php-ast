#ifndef PHP_AST_H
#define PHP_AST_H

#include "php.h"
#include "ast_str_defs.h"

extern zend_module_entry ast_module_entry;
#define phpext_ast_ptr &ast_module_entry

#define PHP_AST_VERSION "1.1.0"

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
	zval metadata;
ZEND_END_MODULE_GLOBALS(ast)

ZEND_EXTERN_MODULE_GLOBALS(ast)

#define AST_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(ast, v)

typedef struct _ast_str_globals {
#define X(str) zend_string *str_ ## str;
	AST_STR_DEFS
#undef X
} ast_str_globals;

extern ast_str_globals str_globals;

#define AST_STR(str) str_globals.str

/* Custom ast kind for names */
#define AST_NAME          2048
#define AST_CLOSURE_VAR   2049
#define AST_NULLABLE_TYPE 2050

// 544 is already taken by ZEND_AST_GROUP_USE
#if PHP_VERSION_ID < 70400
// NOTE: The first hex digit is the number of child nodes a given kind has
# define ZEND_AST_CLASS_NAME 0x1ff
# define ZEND_AST_PROP_GROUP 0x2ff
# define ZEND_AST_ARROW_FUNC 0x5ff
#endif

#if PHP_VERSION_ID < 80000
/* NOTE: For list nodes, the first set bit is 0x80 */
# define ZEND_AST_TYPE_UNION ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 2)
# define ZEND_AST_ATTRIBUTE_LIST ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 3)
# define ZEND_AST_MATCH_ARM_LIST ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 4)
# define ZEND_AST_ATTRIBUTE_GROUP ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 5)
/* 2 child nodes */
# define ZEND_AST_CLASS_CONST_GROUP 0x2fe
# define ZEND_AST_ATTRIBUTE 0x2fd
# define ZEND_AST_MATCH 0x2fc
# define ZEND_AST_MATCH_ARM 0x2fb
# define ZEND_AST_NAMED_ARG 0x2fa
# define ZEND_AST_NULLSAFE_PROP 0x2f9
/* 3 child nodes */
# define ZEND_AST_NULLSAFE_METHOD_CALL 0x3ff
// NOTE: The first hex digit is the number of child nodes a given kind has
#endif

#if PHP_VERSION_ID < 80100
# define ZEND_ACC_ENUM (1 << 22)
# define ZEND_ACC_READONLY (1 << 7)

/* 0 child nodes */
# define ZEND_AST_CALLABLE_CONVERT 3
/* 3 child nodes - name, expr, attributes */
# define ZEND_AST_ENUM_CASE 0x3fe
# define ZEND_AST_TYPE_INTERSECTION ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 6)
#endif

#if PHP_VERSION_ID < 80200
# define ZEND_ACC_READONLY_CLASS (1 << 23)
#endif

/* Pretend it still exists */
# define ZEND_AST_LIST ((1 << (ZEND_AST_IS_LIST_SHIFT + 1)) - 1)

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
