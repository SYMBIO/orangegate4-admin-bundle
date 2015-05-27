<?php

namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Power extends FunctionNode
{
    private $expr = array();

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr[] = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->expr[] = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'POWER('.
            $this->expr[0]->dispatch($sqlWalker).
            ','.
            $this->expr[1]->dispatch($sqlWalker)
            .')';
    }
}