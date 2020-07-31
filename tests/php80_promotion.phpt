--TEST--
Constructor property promotion in PHP 8.0
--SKIPIF--
<?php if (PHP_VERSION_ID < 80000) die('skip PHP >= 8.0 only'); ?>
--FILE--
<?php

require __DIR__ . '/../util.php';

$code = <<<'PHP'
<?php
class Promotion {
	/** Doc comment for __construct */
	public function __construct(
		/** Doc comment for $a */
		public int $a,
		private stdClass &$b = null,
		protected iterable $c = []
	) {
	}
}
PHP;

$node = ast\parse_code($code, $version=70);
echo ast_dump($node), "\n";
--EXPECTF--
AST_STMT_LIST
    0: AST_CLASS
        flags: 0
        name: "Promotion"
        docComment: null
        extends: null
        implements: null
        stmts: AST_STMT_LIST
            0: AST_METHOD
                flags: MODIFIER_PUBLIC (%d)
                name: "__construct"
                docComment: "/** Doc comment for __construct */"
                params: AST_PARAM_LIST
                    0: AST_PARAM
                        flags: PARAM_MODIFIER_PUBLIC (%d)
                        type: AST_TYPE
                            flags: TYPE_LONG (4)
                        name: "a"
                        default: null
                    1: AST_PARAM
                        flags: PARAM_REF | PARAM_MODIFIER_PRIVATE (%d)
                        type: AST_NAME
                            flags: NAME_NOT_FQ (1)
                            name: "stdClass"
                        name: "b"
                        default: AST_CONST
                            name: AST_NAME
                                flags: NAME_NOT_FQ (1)
                                name: "null"
                    2: AST_PARAM
                        flags: PARAM_MODIFIER_PROTECTED (%d)
                        type: AST_TYPE
                            flags: TYPE_ITERABLE (13)
                        name: "c"
                        default: AST_ARRAY
                            flags: ARRAY_SYNTAX_SHORT (3)
                stmts: AST_STMT_LIST
                returnType: null
                __declId: 0
        __declId: 1
