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









}