<?php
/**
 * Created by JetBrains PhpStorm.
 * User: leo
 * Date: 17-12-31
 * Time: 上午9:20
 * To change this template use File | Settings | File Templates.
 */


class Utils {
    public function __construct( ) {
        //$this->logs = LOGS::getInstance();
        $this->_init();
    }

    private function  _init(){

    }

    public function getUserIdInCookie(){
        return isset($_COOKIE['uid'])?$_COOKIE['uid']:'';
    }
}