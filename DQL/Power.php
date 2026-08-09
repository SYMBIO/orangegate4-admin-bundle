<?php

declare(strict_types=1);


namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Power extends FunctionNode
{
    private $expr = array();

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr[] = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->expr[] = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'POWER('.
            $this->expr[0]->dispatch($sqlWalker).
            ','.
            $this->expr[1]->dispatch($sqlWalker)
            .')';
    }
}