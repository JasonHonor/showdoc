<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController 
{
	/*
	 * 访问时用localhost访问的，读出来的是“::1”是正常情况。
	 * ：：1说明开启了ipv6支持,这是ipv6下的本地回环地址的表示。
	 * 使用ip地址访问或者关闭ipv6支持都可以不显示这个。
	 */
	function get_client_ip() {
		$ip = "unknown";
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} elseif (isset($_SERVER["HTTP_CLIENT_ip"])) {
				$ip = $_SERVER["HTTP_CLIENT_ip"];
			} else {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_ip')) {
				$ip = getenv('HTTP_CLIENT_ip');
			} else {
				$ip = getenv('REMOTE_ADDR');
			}
		}
		if(trim($ip)=="::1"){
			$ip="127.0.0.1";
		}
		return $ip;
	}
	
	public function isInternal()
	{
		$hosts=array("192.168.61","172.20.65");
		
		$ip  = $this->get_client_ip(); 				// 获取server变量
		foreach ($hosts as $pos=>$raw)
		{
			$lines = explode(".",$raw);
			$ips = explode(".",$ip);
			
			$match=$lines[0].$lines[1].$lines[2];
			$match2=$ips[0].$ips[1].$ips[2];
			
			if($match==$match2)
				return true;
		}
		
		return false;
	}
	
    public function index(){
        $tmp = @file_get_contents('./Application/Common/Conf/config.php');
        if (strstr($tmp, "showdoc not install")) {
            header("location:./install");
            exit();
        }
        
        if(!$this->isInternal())
        {
        	echo "外部访问";
        	
        	$this->display("public");
        	return;
        }
        
    	$this->checkLogin(false);
    	$login_user = session("login_user");
    	$this->assign("login_user" ,$login_user);
    	if (LANG_SET == 'en-us') {
    		$demo_url = "";
    		$help_url = "";
    		$creator_url = "";
    	}
    	else{
    		$demo_url = "";
    		$help_url = "";
    		$creator_url = "";
    	}
    	
    	$this->assign("internal" ,$this->isInternal());
    	$this->assign("demo_url" ,$demo_url);
    	$this->assign("help_url" ,$help_url);
    	$this->assign("creator_url" ,$creator_url);

        $this->display();
    }
}