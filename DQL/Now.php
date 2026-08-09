<?php

declare(strict_types=1);


namespace Symbio\OrangeGate\AdminBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Custom DQL function returning the difference between two DateTime values
 *
 * usage TIMEDIFF(dateTime1, dateTime2)
 */
class Now extends FunctionNode
{
    /**
     * @var string
     */
    public $dateTime1;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'NOW()';
    }
}