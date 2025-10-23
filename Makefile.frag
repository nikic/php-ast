# Only when building statically into PHP (i.e. not via phpize)
ifeq ($(PHP_AST_SHARED),no)

# Ensure Zend’s generated sources are ready before building ast
$(builddir)/ast.lo: \
  $(top_srcdir)/Zend/zend_language_parser.c \
  $(top_srcdir)/Zend/zend_language_scanner.c

endif
