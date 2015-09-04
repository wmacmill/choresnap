<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Css.php 450 2015-08-12 21:53:07Z timoreithde $
 */ 
class IfwPsn_Util_Parser_Css extends IfwPsn_Util_Parser_Abstract
{
    /**
     * @param $css
     * @return mixed
     */
    public static function sanitize($css)
    {
        $css = self::stripNullByte($css);

        return $css;
    }
}
