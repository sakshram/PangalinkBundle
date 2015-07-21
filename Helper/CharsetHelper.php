<?php
namespace TFox\PangalinkBundle\Helper;

class CharsetHelper
{

    public static function utf8ToIso88591($input)
    {
        return iconv('UTF-8', 'ISO-8859-1', $input);
    }
}