<?php


function fwttrans($content) {
    $res = qtranxf_get_language_blocks($content);
    //return qtranxf_get_language_blocks($q_config['language']);
    return 'TEST :: ' . $content;
}

add_filter('the_content', 'fwttrans_useCurrentLanguage', 100);
