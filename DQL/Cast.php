<?php

declare(strict_types=1);


namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Cast extends FunctionNode
{
    public $expressionToCast = null;
    public $cast = null;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expressionToCast = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->cast = $lexer->token->value;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s %s', $this->cast, $this->expressionToCast->dispatch($sqlWalker));
    }
}
