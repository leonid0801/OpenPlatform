<?php


$link1 = '<a href="http://ui.ptlogin2.qq.com/cgi-bin/login?style=9&appid=46000101&daid=1&s_url=http://awdt.wecar.qq.com/CommercePush/wx/verify?wxopenid=123333&low_login=0&hln_u_tips=%E8%AF%B7%E8%BE%93%E5%85%A5WECAR%E4%BC%9A%E5%91%98QQ%E8%B4%A6%E5%8F%B7&hln_p_tips=%E8%AF%B7%E8%BE%93%E5%85%A5%E5%AF%86%E7%A0%81&hln_login=%E7%BB%91%20%E5%AE%9A">帐号绑定</a>';
$link2 = '<a href="http://crm.wecar.qq.com/dealer/home/register">申请成为WECAR会员</a>';

$line1 = '欢迎关注WECAR商机监控！';
$line2 = '本公众帐号是服务于腾讯汽车WECAR经销商会员的专属帐号，'; 
$line3 = '如果您确认是WECAR会员，请点击“商机监控”进入' . $link1.';' ;
$line4 = '如果您不是WECAR会员，但从事汽车经销商的工作，可以'.$link2 .'。';

$g_welcome = $line1 ."\n". $line2. "\n" .$line3 ."\n".$line4. "\n" ;




$g_binding_success = "绑定成功！\n现在您可以监控您所在汽车经销商的商机，并收到即时商机提醒" ; 


define("WX_COMMERCE_PUSH_GLOBALACCOUNT",'gh_c35951a0ec26') ;
define("WX_COMMERCE_PUSH_APPID",'wx8f57106017a8df42');
define("WX_COMMERCE_PUSH_APPSECRET",'fbfdb34a2823ddb413e1b785f0652c9f');

define("WX_ACCESSTOKEN",'https://api.weixin.qq.com/cgi-bin/token');

define("WX_COMMERCE_PUSH_STATE",'wxcommercepush');




?>
