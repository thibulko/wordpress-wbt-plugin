<?php

class Fwt_Widget_Switcher extends WP_Widget {

    protected $config;

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
        echo '
            <label>Language</label>
            <select style="width:100%;">
                <option>Ukraine</option>
                <option>Russian</option>
            </select>
        ';
    }

    private function get_aviable_langs(){
        //$this->config->get_option();
    }


}
?>