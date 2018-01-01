<?php

ini_set('display_errors',1);            //������Ϣ
ini_set('display_startup_errors',1);    //php����������Ϣ
error_reporting(-1);                    //��ӡ�����е� ������Ϣ

//define ( "APP_PATH", realpath (  dirname(__FILE__) ) ) ;
require dirname(__FILE__) . '/config/config.php';


include_once(APP_PATH."/application/Application.php");
include_once(APP_PATH."/application/view/View.php");
include_once(APP_PATH."/application/controller/BaseController.php");
include_once(APP_PATH."/application/utils/Utils.php");
include_once(APP_PATH."/application/module/Items.php");
include_once(APP_PATH."/application/module/Action.php");
#include_once(APP_PATH."/application/controller/WxController.php");
include_once(APP_PATH."/application/Logs.php");
include_once(APP_PATH."/application/model/LogicBase.php");
include_once(APP_PATH."/application/model/DBModel.php");
include_once(APP_PATH."/application/model/WxAppModel.php");
include_once(APP_PATH."/application/model/ItemModel.php");
include_once(APP_PATH."/application/model/UserModel.php");
include_once(APP_PATH."/application/model/ImageModel.php");
include_once(APP_PATH."/application/model/User.php");
include_once(APP_PATH."/application/model/Item.php");
include_once(APP_PATH."/application/model/item/Image.php");
include_once(APP_PATH."/application/model/ItemNewModel.php");

#include_once(APP_PATH."/application/model/ImageModel.php");

$application = new Application ();

$application->run ();


?>
