<?php

/** Dumps abstract syntax tree */
function ast_dump($ast) {
    if ($ast instanceof ast\Node) {
        $result = ast\getKindName($ast->kind);
        $result .= " @ $ast->lineno";
        if (isset($ast->endLineno)) {
            $result .= "-$ast->endLineno";
        }
        $result .= " {";
        if (ast\kindUsesFlags($ast->kind)) {
            $result .= "\n    flags: $ast->flags";
        }
        if (isset($ast->docComment)) {
            $result .= "\n    docComment: $ast->docComment";
        }
        foreach ($ast->children as $i => $child) {
            $result .= "\n    $i: " . str_replace("\n", "\n    ", ast_dump($child));
        }
        $result .= "\n}";
        return $result;
    } else if ($ast === null) {
        return 'null';
    } else if (is_string($ast)) {
        return "\"$ast\""; 
    } else {
        return (string) $ast;
    }
}
