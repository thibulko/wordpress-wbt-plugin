<?php

class FwtWidgetSwitcher extends WP_Widget {

    private $container;

    public function __construct($container)
    {
        $this->container = $container;

        parent::__construct(
            'fwt_switcher_widget',
            __('FWT Languages switcher', 'fwt_switcher_widget_domain'),
            array(
                'description' => __('Widget wich add to theme languages switcher')
            )
        );
    }

    //frontend of widget
    public function widget( $args, $instance )
    {
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        $tmp = '<label>Language</label><select  onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" style="width:100%;">';
            $tmp .= '<option value="">--------</option>';
        if( !empty($this->languages()) ){
            foreach( $this->languages() as $lang ){
                $tmp .= '<option value="'.$this->get_link().'?lang='.$lang['code'].'">'.$lang['name'].'</option>';
            }
        }
        $tmp .='</select>';

        echo $tmp;
    }

    private function languages()
    {
        return $this->container->get('config')->getLanguages();
    }

    private function get_link()
    {
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));

        return $current_url;
    }

    public function init()
    {
        register_widget( $this );
    }
}