/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 3e43ae41473e7ac0111f3c1a8a7df724e1d60232 */

ZEND_BEGIN_ARG_INFO_EX(arginfo_ast_parse_code, 0, 0, 2)
	ZEND_ARG_INFO(0, code)
	ZEND_ARG_INFO(0, version)
	ZEND_ARG_INFO(0, filename)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_ast_parse_file, 0, 0, 2)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, version)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_ast_get_kind_name, 0, 0, 1)
	ZEND_ARG_INFO(0, kind)
ZEND_END_ARG_INFO()

#define arginfo_ast_kind_uses_flags arginfo_ast_get_kind_name

ZEND_BEGIN_ARG_INFO_EX(arginfo_ast_get_metadata, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_ast_get_supported_versions, 0, 0, 0)
	ZEND_ARG_INFO(0, exclude_deprecated)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_ast_Node___construct, 0, 0, 0)
	ZEND_ARG_INFO(0, kind)
	ZEND_ARG_INFO(0, flags)
	ZEND_ARG_INFO(0, children)
	ZEND_ARG_INFO(0, lineno)
ZEND_END_ARG_INFO()

#define arginfo_class_ast_Node_parseCode arginfo_ast_parse_code

#define arginfo_class_ast_Node_parseFile arginfo_ast_parse_file


ZEND_FUNCTION(parse_code);
ZEND_FUNCTION(parse_file);
ZEND_FUNCTION(get_kind_name);
ZEND_FUNCTION(kind_uses_flags);
ZEND_FUNCTION(get_metadata);
ZEND_FUNCTION(get_supported_versions);
ZEND_METHOD(ast_Node, __construct);
ZEND_METHOD(ast_Node, parseCode);
ZEND_METHOD(ast_Node, parseFile);


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
	ZEND_ME(ast_Node, parseFile, arginfo_class_ast_Node_parseFile, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	ZEND_FE_END
};
