<?php

class FwtHttpClient extends FwtAbstract
{
    protected $BASE_URL = '';

    protected $params = array(
        'method' => "GET",
        'user-agent' => 'WEB Translator'
    );

    public function setBaseUrl($url)
    {
        $this->BASE_URL = $url;
    }

    public function getBaseUrl()
    {
        return rtrim($this->BASE_URL, '/') . '/';
    }

    public function remote($url, $params = [])
    {
        $url = $this->getBaseUrl()  . $url;

        $request = wp_remote_request($url, array_merge($this->params, $params));

        $code = wp_remote_retrieve_response_code( $request );
        $mesg = wp_remote_retrieve_response_message( $request );
        $body = json_decode(wp_remote_retrieve_body( $request ), true );

        //$this->log($url);

        if ( 200 != $code && !empty( $mesg ) ) {
            $this->addError($code, $mesg);
            return false;
        } elseif ( 200 != $code ) {
            $this->addError($code, 'Unknown error!');
            return false;
        } elseif( !$body ) {
            $this->addError('nodata', 'Data not found.');
            return false;
        } else {
            return $body;
        }
    }
}