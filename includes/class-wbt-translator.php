<?php

class WbtTranslator
{
    protected $languages = [];

    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     *  Join functions
     *
     * @param $texts
     * @return string
     */
    public function join($texts)
    {
        if (!is_array($texts)) {
            return $texts;
        }

        $text = '';

        foreach ($texts as $lang => $val) {
            if (!empty($val)) {
                $text .= '[:' . $lang . ']' . $val;
            }
        }

        if (strlen($text) > 0) {
            $text .= '[:]';
        }

        return $text;
    }

    /**
     *  Split functions
     *
     * @param $text
     * @return array
     */
    function split($text)
    {
        $blocks = $this->getLanguageBlocks($text);
        return $this->splitBlocks($blocks);
    }

    public function getLanguageBlocks($text)
    {
        $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        return preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public function splitBlocks($blocks)
    {
        $result = array();
        $current_language = false;
        $languages = $this->getLanguages();

        if (empty($languages)) {
            return $blocks;
        }

        foreach ($languages as $language) {
            $result[$language['code']] = '';
        }

        foreach ($blocks as $block) {
            // detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }

            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    // correctly categorize text block
                    if ($current_language) {
                        if (!isset($result[$current_language])) {
                            $result[$current_language] = '';
                        }

                        $result[$current_language] .= $block;
                        $current_language = false;
                    } else {
                        foreach ($languages as $language) {
                            $result[$language['code']] .= $block;
                        }
                    }
                    break;
            }
        }

        foreach ($result as $lang => $text) {
            $result[$lang] = trim($text);
        }

        return $result;
    }

    public function splitLanguages($blocks)
    {
        $result = array();
        $current_language = false;

        foreach ($blocks as $block) {
            // detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }

            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    // correctly categorize text block
                    if ($current_language) {
                        if (!isset($result[$current_language])) $result[$current_language] = '';
                        $result[$current_language] .= $block;
                        $current_language = false;
                    }
                    break;
            }
        }

        foreach ($result as $lang => $text) {
            $result[$lang] = trim($text);
        }

        return $result;
    }
}