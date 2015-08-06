<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Html.php 432 2015-06-07 22:17:57Z timoreithde $
 */ 
class IfwPsn_Util_Parser_Html 
{
    /**
     * @param $html
     * @return mixed
     */
    public static function stripScript($html)
    {
        do {
            if (isset($result)) {
                $html = $result;
            }
            $result = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
        } while ($result != $html);

        return self::stripNullByte($result);
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function stripNullByte($string)
    {
        return str_replace(chr(0), '', $string);
    }
}
