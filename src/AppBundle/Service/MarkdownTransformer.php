<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 03.01.18
 * Time: 15:37
 */

namespace AppBundle\Service;


class MarkdownTransformer
{
    public function parse($str)
    {
        return strtoupper($str);
    }
}