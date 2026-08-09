<?php

declare(strict_types=1);


namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Collate extends FunctionNode
{
    public $expressionToCollate = null;
    public $collation = null;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expressionToCollate = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->collation = $lexer->token->value;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s COLLATE %s', $this->expressionToCollate->dispatch($sqlWalker), $this->collation);
    }
}
