<?php
defined('SECRET') && SECRET === "@bayramlcm" OR exit('Erişiminiz Engellendi');

class Minify {

    function __construct()
    {
        ob_start(["self", "minify2"]);
    }

    // Minify 1
    static function minify1($buffer) {

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
    
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
    
        $buffer = preg_replace($search, $replace, $buffer);
    
        return $buffer;
    }

    // Minify 2
    static function minify2($html)
    {
       $search = array(
        '/(\n|^)(\x20+|\t)/',
        '/(\n|^)\/\/(.*?)(\n|$)/',
        '/\n/',
        '/\<\!--.*?-->/',
        '/(\x20+|\t)/', # Delete multispace (Without \n)
        '/\>\s+\</', # strip whitespaces between tags
        '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
        '/=\s+(\"|\')/'); # strip whitespaces between = "'
    
       $replace = array(
        "\n",
        "\n",
        " ",
        "",
        " ",
        "><",
        "$1>",
        "=$1");
    
        $html = preg_replace($search,$replace,$html);
        return $html;
    }

}