<?php

class FwtHttpClient extends FwtAbstract
{
    const BASE_URL = 'http://192.168.88.149:8080/api/v2/';

    protected $params = array(
        'method' => "GET",
        'user-agent' => 'WEB Translator'
    );

    public function remote($url, $params = [])
    {
        $url = rtrim(self::BASE_URL, '/') . '/' . $url;

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