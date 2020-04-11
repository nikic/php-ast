/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: ea3081072dd7d8bda1aa2fa6dcb5be82ae88491a */

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_parse_code, 0, 2, ast\\Node, 0)
	ZEND_ARG_TYPE_INFO(0, code, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, version, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, filename, IS_STRING, 0, "\'string code\'")
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_parse_file, 0, 2, ast\\Node, 0)
	ZEND_ARG_TYPE_INFO(0, filename, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, version, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_get_kind_name, 0, 1, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_kind_uses_flags, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_get_metadata, 0, 0, IS_ARRAY, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_get_supported_versions, 0, 0, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, exclude_deprecated, _IS_BOOL, 0, "false")
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_Node___construct, 0, 0, 0)
	ZEND_ARG_INFO_WITH_DEFAULT_VALUE(0, kind, "null")
	ZEND_ARG_INFO_WITH_DEFAULT_VALUE(0, flags, "null")
	ZEND_ARG_INFO_WITH_DEFAULT_VALUE(0, children, "null")
	ZEND_ARG_INFO_WITH_DEFAULT_VALUE(0, lineno, "null")
ZEND_END_ARG_INFO()
