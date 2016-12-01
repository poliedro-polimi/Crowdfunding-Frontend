<?php
namespace nigiri\views;

/**
 * Provides some utilities to print html code
 * @package site\views
 */
class Html{

    /**
     * Escapes UTF8 characters to html entities to avoid xss attacks and strange symbols appearing on screen
     * @param $str
     * @return string the escaped string
     */
    public static function escape($str){
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Codes an HTML tag.
     * @param string $name the name of the tag to make (e.g. a, div, span...)
     * @param string $content the content of the tag, it can be any string even HTML code. IT DOES NOT GET ESCAPED
     * @param array $attributes additional attributes to set in the tag (e.g. href, alt, title, style). Array keys should be attributes names and array values should be their values
     * @return string the HTML code of the tag
     */
    public static function tag($name, $content='', $attributes=[]){
        $out = '<'.$name.' ';
        $attr = [];
        foreach($attributes as $k=>$v){
            $attr[] = $k.'="'.str_replace('"', '\\"', $v).'"';
        }

        return $out.implode(' ', $attr).'>'.$content.'</'.$name.'>';
    }

    /**
     * Makes the code of an HTML link
     * @param string $to url which to point to
     * @param string $text the content of the tag, IT DOES NOT GET ESCAPED
     * @param array $attributes additional attributes to set in the tag
     * @return string the HTML code of the link
     */
    public static function a($to, $text, $attributes = []){
        return self::tag('a', $text, array_merge($attributes, ['href' => $to]));
    }
}
