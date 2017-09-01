<?php

class WbtPublic extends WbtAbstract
{
    public function the_content($content)
    {
        $current_language = !empty($_GET['lang']) ? $_GET['lang'] : 'en';

        $content = $this->container()->get('translator')->split($content);

        if (!is_array($content)) {
            return $content;
        }

        return isset($content[$current_language]) ? $content[$current_language] : $content[key($content)];
    }
}