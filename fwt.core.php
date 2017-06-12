<?php

function qtranxf_get_language_blocks($text) {
    $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
    return preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
}
/*function qtranxf_use($lang, $text, $show_available=false, $show_empty=false) {
    //global $q_config;
    // return full string if language is not enabled
    //if(!qtranxf_isEnabled($lang)) return $text;//why?
    if(is_array($text)) {
        // handle arrays recursively
        foreach($text as $key => $t) {
            $text[$key] = qtranxf_use($lang,$t,$show_available,$show_empty);
        }
        return $text;
    }

    if( is_object($text) || $text instanceof __PHP_Incomplete_Class ) {//since 3.2-b1 instead of @get_class($text) == '__PHP_Incomplete_Class'
        foreach(get_object_vars($text) as $key => $t) {
            if(!isset($text->$key)) continue;
            $text->$key = qtranxf_use($lang,$t,$show_available,$show_empty);
        }
        return $text;
    }

    // prevent filtering weird data types and save some resources
    if(!is_string($text) || empty($text))//|| $text) == ''
        return $text;

    return qtranxf_use_language($lang, $text, $show_available, $show_empty);
}*/

/*function qtranxf_use_language($lang, $text, $show_available=false, $show_empty=false) {
    $blocks = qtranxf_get_language_blocks($text);
    if(count($blocks)<=1)//no language is encoded in the $text, the most frequent case
        return $text;
    return qtranxf_use_block($lang, $blocks, $show_available, $show_empty);
}*/
