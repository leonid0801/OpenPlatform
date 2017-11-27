<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-11-26
 * Time: 下午8:58
 * To change this template use File | Settings | File Templates.
 */



class LogicBase {
    public function __construct( ) {
        $this->logs = LOGS::getInstance();
        $this->_init();
    }



    /**
     * 访问接口收集返回数据
     *
     * @param unknown $url
     * @param string $files
     * @return mixed
     */
    public function curl($url, $files = "") {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $files );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_COOKIEFILE, "" );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }


    public function filter($str) {
        if($str){
            $tmpStr = json_encode($str);
            $return = json_decode(preg_replace("/(\\\u[ed][0-9a-f]{3})/i","",$tmpStr));
        }else{
            $return = '';
        }
        return $return;

    }









}