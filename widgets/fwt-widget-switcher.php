<?php

class Fwt_Widget_Switcher extends WP_Widget {

    private $config;

    public function __construct($config) {
        $this->config = $config;

        parent::__construct(
            'fwt_switcher_widget',
            __('FWT Languages switcher', 'fwt_switcher_widget_domain'),
            array(
                'description' => __('Widget wich add to theme languages switcher')
            )
        );
    }

    //frontend of widget
    public function widget( $args, $instance ){
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        $tmp = '<label>Language</label><select  onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" style="width:100%;">';
        if( !empty($this->get_aviable_langs()) ){
            foreach( $this->get_aviable_langs() as $lang ){
                $tmp .= '<option value="'.$this->get_link().'?lang='.$lang.'">'.$lang.'</option>';
            }
        }
        $tmp .='</select>';

        echo $tmp;
    }

    private function get_aviable_langs(){
        return $this->config->get_languages();
    }

    private function get_link(){
        $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } 
        else 
        {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }


}
?>