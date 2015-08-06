<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: AdminComments.php 393 2015-02-17 22:53:26Z timoreithde $
 * @package   
 */ 
class IfwPsn_Util_Parser_AdminComments 
{
    /**
     * @var array
     */
    protected static $_allowedTags = array(
        '<a>',
        '<b>',
        '<br>',
        '<div>',
        '<em>',
        '<p>',
        '<span>',
        '<ul>',
        '<li>',
    );

    /**
     * @return array
     */
    public static function getAllowedTags()
    {
        return self::$_allowedTags;
    }

    public static function addAllowedTag($tag)
    {
        //array_push(self::$_allowedTags, $tag);
    }

    /**
     * @param $text
     */
    public static function parse($text)
    {
        return nl2br(strip_tags(html_entity_decode($text), implode('', self::getAllowedTags())));
    }
}
