dnl config.m4 for extension ast

PHP_ARG_ENABLE(ast, whether to enable ast support,
[  --enable-ast            Enable ast support])

if test "$PHP_AST" != "no"; then
  PHP_NEW_EXTENSION(ast, ast.c ast_data.c, $ext_shared)
fi
