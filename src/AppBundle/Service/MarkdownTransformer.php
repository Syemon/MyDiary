<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 03.01.18
 * Time: 15:37
 */

namespace AppBundle\Service;


use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    private $markdownParser;

    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
        return $this->markdownParser
            ->transformMarkdown($str);
    }

}