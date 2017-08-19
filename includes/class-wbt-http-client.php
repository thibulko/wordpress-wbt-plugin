<?php

class WbtHttpClient extends WbtAbstract
{
    protected $baseUrl = '';
    
    protected $params = array(
        'method' => "GET",
        'user-agent' => 'WBTranslator'
    );
    
    public function __construct($container = null)
    {
        parent::__construct($container);
        
        if (defined('WBT_API_URL')) {
            $this->setBaseUrl(WBT_API_URL);
        }
    }
    
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getBaseUrl()
    {
        return rtrim($this->baseUrl, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    
    public function getApiKey()
    {
        return $this->container()->get('config')->getApiKey();
    }
    
    public function remote($url, $params = [])
    {
        $url = $this->getBaseUrl() . $this->getApiKey() . DIRECTORY_SEPARATOR . ltrim($url, DIRECTORY_SEPARATOR);

        $request = wp_remote_request($url, array_merge($this->params, $params));

        $code = wp_remote_retrieve_response_code( $request );
        $mesg = wp_remote_retrieve_response_message( $request );
        $body = json_decode(wp_remote_retrieve_body( $request ), true );
    
        if (200 != $code) {
            if (!empty($body['message'])) {
                if (is_array($body['message'])) {
                    $mesg = reset($body['message']);
                }
            }
            throw new \Exception((!empty($mesg) ? $mesg : 'Unknown error!'), $code);
        }
        
        return $body;
    }
}