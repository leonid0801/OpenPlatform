<?php
require dirname(__FILE__) . '/Wx.conf.php';
//define your token
define("TOKEN", "weixin");



class Api_Wx extends BaseController{

	
	
	public function _init() {

        
    }
	public function test(){
		var_dump($_GET);
		echo "Here!";
	}
	public function m(){
	
		# valid 用于微信服务器配置验证
		///$this->valid(); // 用于验证配置
		
		$this->here();
		//$this->responseMsg();
		/*$wx_info = array(
			'Fopenid' => "123456",       ///
                        //'Fqq' => $qqnum, 
                        //'Fdealer_id' => $dealer_id,
						//'Finsert_time' => date("Y-m-d H:i:s",time() ) 
        );
			
        $this->wxUserModel->addWxOpenid($wx_info);
		echo "***";*/
	
	
		/*$view = $this->_getView();
		$info1 = array("status"=>"1","id"=>"1","city"=>"city","commerce_type"=>"2","commerce_name"=>"3","date"=>"2015");
		$info2 = array("status"=>"2","id"=>"1","city"=>"city","commerce_type"=>"2","commerce_name"=>"3","date"=>"2015");
		$info3 = array("status"=>"3","id"=>"1","city"=>"city","commerce_type"=>"2","commerce_name"=>"3","date"=>"2015");
		
		//$info1 = array("status"=>"1","id"=>"1","city"=>"city","commerce_type"=>"order","commerce_name"=>"3","date"=>"2015","phone"=>"136","carinfo"=>"car","eff_con"=>"succ");
		
		
		$userinfo[] = $info1;
		$userinfo[] = $info2;
		$userinfo[] = $info3;
 		$view->assign('userinfo',$info1);
		$view->render('list')   ;
		*/
	
	
	}
	
	
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        echo $echoStr;
        //valid signature , option
        /*if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }*/
    }
	
	public function here(){
		$this->getPost ();
	}

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		#$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postStr = file_get_contents("php://input");

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                
				$fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	private function welcomeText($wxopenid){
        /*$link1 = '<a href="http://ui.ptlogin2.qq.com/cgi-bin/login?style=9&appid=46000101&daid=1&s_url=http://wecar.qq.com/CommercePush/wx/verify?wxopenid='.$wxopenid.'&low_login=0&hln_u_tips=%E8%AF%B7%E8%BE%93%E5%85%A5WECAR%E4%BC%9A%E5%91%98QQ%E8%B4%A6%E5%8F%B7&hln_p_tips=%E8%AF%B7%E8%BE%93%E5%85%A5%E5%AF%86%E7%A0%81&hln_login=%E7%BB%91%20%E5%AE%9A">你好6</a>'; 
        $link2 = '<a href="http://leonid.sinaapp.com/enroll.html">在这里</a>';
        $line1 = '你好';
        $line2 = '欢迎关注梧桐下漫步公众号';
        $line3 = '你好33' . $link1.';' ;
        $line4 = '报名'.$link2;
        $welcome = $line1 ."\n". $line2. "\n" .$line3 ."\n".$line4. "\n" ;
        return $welcome ;*/

        $link2 = '<a href="https://www.sojump.hk/m/9861235.aspx">在这里</a>';
        $line1 = '你好，欢迎关注手模公众号，';
        $line4 = '预选报名'.$link2;
        $welcome = $line1 ."\n" . $line4. "\n" ;
        return $welcome ;
    }
	
	private function BuildTextMsg($message, $content)
    {
        $template = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
        </xml>";
		// $toUserName 用户的openid
        $toUserName = $message['FromUserName'];
		
		// $fromUserName 公众号id
        $fromUserName = $message['ToUserName'];
        $createTime = time(); 

        $result = sprintf($template, $toUserName, $fromUserName, $createTime, $content);

        return $result;
    }
	
	private function serverResponse($message){

        //require dirname(__FILE__) . '/WxCommercePush.conf.php';       
        
        $msgtype = $message['MsgType'];
        $event = $message['Event'];

        $userOpenid = $message['FromUserName'];
        $wx_info = array('Fwx_openid' => $userOpenid);
		
  
        
        if ($msgtype == 'event' && $event == 'subscribe')
        {       
			#$user_infos = $this->wxUserModel->getUserByOpenid($userOpenid)   ;  
			/*if (false == $user_infos ){
				$wx_info = array(
					'Fopenid' => $userOpenid,       ///
					//'Fqq' => $qqnum, 
					//'Fdealer_id' => $dealer_id,
					'Fcreate_time' => date("Y-m-d H:i:s",time() ),
					'Fupdate_time' => date("Y-m-d H:i:s",time() ) 
				);
				#$this->wxUserModel->addWxOpenid($wx_info);
			}*/  
			
			$res = $this->BuildTextMsg( $message,$this->welcomeText($userOpenid) );
        }

		if ($msgtype == 'event' && $event == 'VIEW'){

		}
		
		if ($msgtype == 'location' ){
			#$user_infos = $this->wxUserModel->getUserByOpenid($userOpenid)   ;
			if (false == $user_infos ){
				$wx_info = array(
					'Fopenid' => $userOpenid,       ///
					//'Fqq' => $qqnum, 
					//'Fdealer_id' => $dealer_id,
					'Fcreate_time' => date("Y-m-d H:i:s",time() ),
					'Fupdate_time' => date("Y-m-d H:i:s",time() ) 
				);
				#$this->wxUserModel->addWxOpenid($wx_info);
				$res = $this->BuildTextMsg( $message,'请重新上传位置' );
			}else{
				$uid = $user_infos[0]['Fid'];
				$uploadUrl = "http://leonid.applinzi.com/index.php/user/profile/test1?uid=".$uid;
				$res = $this->BuildTextMsg( $message,'位置发送成功, <a href="'.$uploadUrl.'">点此上传照片</a>' );
			}

		}
		
		if ($msgtype == 'image' ){
			$res = $this->BuildTextMsg( $message,'good' );
		}
		
		if ($msgtype == 'text' ){
			$res = $this->BuildTextMsg( $message,'你好;Hi' );
		}
        
		/*
        if ($msgtype == 'event' && $event == 'CLICK'){
            $view = $this->_getView();
            $view->assign('retcode',0);
            $view->render('loginsuccess')   ;
            break ; 
        }       
*/
        echo $res ;

    }
	
	
		/**
     * 
     */
    private function getPost() {
        #$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");

        if (! empty ( $postStr )) {
            $postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            $post_data ['fromUsername'] = trim ( $postObj->FromUserName );
            $post_data ['FromUserName'] = $post_data ['fromUsername'];
            $post_data ['toUsername'] = trim ( $postObj->ToUserName );
            $post_data ['ToUserName'] = $post_data ['toUsername'];
            $post_data ['Message'] = trim ( $postObj->Content );
            $post_data ['Content'] = trim ( $postObj->Content );
            $post_data ['CreateTime'] = trim ( $postObj->CreateTime );
            $post_data ['Location_X'] = trim ( $postObj->Location_X );
            $post_data ['Location_Y'] = trim ( $postObj->Location_Y );
            $post_data ['Scale'] = trim ( $postObj->Scale );
            $post_data ['Label'] = trim ( $postObj->Label );
            $post_data ['PicUrl'] = trim ( $postObj->PicUrl );
            $post_data ['MsgType'] = trim ( $postObj->MsgType );
            $post_data ['MsgId'] = trim ( $postObj->MsgId );
            $post_data ['Url'] = trim ( $postObj->Url );
            $post_data ['Event'] = trim ( $postObj->Event );
            $post_data ['Latitude'] = trim ( $postObj->Latitude );
            $post_data ['Longitude'] = trim ( $postObj->Longitude );
            $post_data ['Precision'] = trim ( $postObj->Precision );
            $post_data ['EventKey'] = trim ( $postObj->EventKey );
            //$post_data ['token'] = WEIXIN_TOKEN;
        
            ///print_r ( $post_data );
            ///$this->saveData ( $post_data ['MsgType'], $post_data );
        
            ///$this->responseData ( $post_data ['MsgType'],$post_data );
            $this->serverResponse($post_data);  

        } else {
            echo "";
            exit ();
        }       
    }
	
	
	
	
	
	public function menuInit(){

		$qs = array(			
			'grant_type' => 'client_credential',			
			'appid' => WX_COMMERCE_PUSH_APPID,			
			'secret' => WX_COMMERCE_PUSH_APPSECRET,		
		);

		$url = $this->buildUrl(WX_ACCESSTOKEN,$qs);

		$res = $this->curl($url);
		if (false == $res){
			//return false ;
		}
		$access_token = json_decode($res)->access_token ;
		$menu_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
		
		$url = "https://www.sojump.hk/m/9861235.aspx";
		//$url = "http://awdt.wecar.qq.com/CommercePush/wx/commerceMonitor";

		$info1['type'] = 'view';
		$info1['name'] = urlencode('豆瓣');
		$info1['url'] = "https://www.douban.com/group/453778/" ;
		$info2['type'] = 'view';
		$info2['name'] = urlencode('社区');
		$info2['url'] = "http://m.wsq.qq.com/263961037" ;
		$info3['type'] = 'view';
		$info3['name'] = urlencode('报名');
		//$info3['url'] = "http://weidian.com/s/310571848?wfr=c" ;
		$info3['url'] = $url ;
		$menu['button'][] = $info1 ;
		$menu['button'][] = $info2 ;
		$menu['button'][] = $info3 ;
		
		$post_data = json_encode($menu) ;
		# 解决菜单中文字符编码问题
		$post_data = urldecode($post_data); 
		

		$ret = $this->curl($menu_url,$post_data);
		
		echo $ret ;

	}




}



?>
