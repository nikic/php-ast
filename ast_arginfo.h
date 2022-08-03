/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 938fe8988fbcf94dd83623b485367a9d77446be2 */

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_ast_parse_code, 0, 2, ast\\Node, 0)
	ZEND_ARG_TYPE_INFO(0, code, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, version, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, filename, IS_STRING, 0, "\'string code\'")
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_ast_parse_file, 0, 2, ast\\Node, 0)
	ZEND_ARG_TYPE_INFO(0, filename, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, version, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_ast_get_kind_name, 0, 1, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_ast_kind_uses_flags, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_ast_get_metadata, 0, 0, IS_ARRAY, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_ast_get_supported_versions, 0, 0, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, exclude_deprecated, _IS_BOOL, 0, "false")
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_ast_Node___construct, 0, 0, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, kind, IS_LONG, 1, "null")
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, flags, IS_LONG, 1, "null")
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, children, IS_ARRAY, 1, "null")
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, lineno, IS_LONG, 1, "null")
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_ast_Node_parseCode, 0, 2, IS_STATIC, 0)
	ZEND_ARG_TYPE_INFO(0, code, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, version, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, filename, IS_STRING, 0, "\'string code\'")
ZEND_END_ARG_INFO()


ZEND_FUNCTION(parse_code);
ZEND_FUNCTION(parse_file);
ZEND_FUNCTION(get_kind_name);
ZEND_FUNCTION(kind_uses_flags);
ZEND_FUNCTION(get_metadata);
ZEND_FUNCTION(get_supported_versions);
ZEND_METHOD(ast_Node, __construct);
ZEND_METHOD(ast_Node, parseCode);


static const zend_function_entry ext_functions[] = {
	ZEND_NS_FE("ast", parse_code, arginfo_ast_parse_code)
	ZEND_NS_FE("ast", parse_file, arginfo_ast_parse_file)
	ZEND_NS_FE("ast", get_kind_name, arginfo_ast_get_kind_name)
	ZEND_NS_FE("ast", kind_uses_flags, arginfo_ast_kind_uses_flags)
	ZEND_NS_FE("ast", get_metadata, arginfo_ast_get_metadata)
	ZEND_NS_FE("ast", get_supported_versions, arginfo_ast_get_supported_versions)
	ZEND_FE_END
};


static const zend_function_entry class_ast_Node_methods[] = {
	ZEND_ME(ast_Node, __construct, arginfo_class_ast_Node___construct, ZEND_ACC_PUBLIC)
	ZEND_ME(ast_Node, parseCode, arginfo_class_ast_Node_parseCode, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	ZEND_FE_END
};
