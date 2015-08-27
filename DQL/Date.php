<?php

namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Custom DQL function returning the difference between two DateTime values
 *
 * usage TIMEDIFF(dateTime1, dateTime2)
 */
class Date extends FunctionNode
{
    /**
     * @var string
     */
    public $dateTime1;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateTime1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'DATE(' .
        $this->dateTime1->dispatch($sqlWalker) .
        ')';
    }
}