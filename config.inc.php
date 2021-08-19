<?php
require_once 'class.MyConfig.php';
class Config extends MyConfig{
	const
		DB_USER="root", DB_PW="053721872",
		EMPLOYEE_WORK_HOUR=8, // hour
		
		MEMBER_EXP_YEAR=1, // year
		MEMBER_PRE_RENEW_M=2, //month
		MEMBER_POST_RENEW_M=12, //month
		MEMBER_REGISTER_FEE=200, //฿
		MEMBER_RENEW_FEE=150, //฿
		
		CLEAR_EXP_MEMBER=3, // year
		CLEAR_ORDER=2, // year
		CLEAR_ACCOUNT=10, // year
		CLEAR_EMPLOYEE_LOG=2, //year
		CLEAR_CUSTOMER_STAT=5, //year
		
		SLIP_WIDTH=58, //58 mm and 80 mm
		SLIP_HEIGHT=75,
		PRINT_MAX_LOOP=5,
		PRINT_FOXIT_PATH='FoxitReaderPortable\\App\\Foxit Reader\\Foxit Reader.exe', // FOR Windows
		
		CASHIER_PRINTER='';
	public function PDO(){
		$dbh=new PDO(
			"mysql:host=localhost;dbname=resturant;", // DSN
			self::DB_USER,self::DB_PW
		);
		$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}
	public static function isAjax($checkSession=false,$checkCookie=false,$pin=false){
		$ajax=isset($_GET['ajax']);
		if($checkSession||$checkCookie){
			require_once 'class.Session.php';
			require_once 'class.SKAjax.php';
			$sess=new Session();
			if(!$sess->load()->checkSession()){ //If don't have session
				$rpn=new SKAjax();
				if(!$ajax){
					header("Location: login.php");
					exit("Need Log in authenication");
				}elseif($pin && $sess->loadCookie(self::PDO()) && !$sess->isAuth(Session::AUTH_REJECT_PIN))
					$rpn->message="Need PIN authenication";
				elseif($sess->isAuth(Session::AUTH_REJECT_LOGIN))
					$rpn->addAction(SKAjax::REDIRECT,"login.php");
				else
					$rpn->message="Need Log in authenication";
				self::JSON($rpn);
			}elseif($checkCookie && !$sess->checkCookie()){ //if check cookie and no cookie
				if($ajax){
					$rpn=new SKAjax();
					$rpn->addAction(SKAjax::ALERT,'กรุณาเข้างานก่อน แล้ว log in อีกครั้ง');
					$rpn->addAction(SKAjax::REDIRECT,'logout.php');
					self::JSON();
					exit($rpn);
				}else{
					header("Location: logout.php");
					exit("You do not have permission to see it. Because you do not sign in for working.");
				}
			}elseif(!($sess->perms($checkSession) || $checkSession===true)){
				header("Location: ./");
				exit("You do not have permission to see it.");
			}
		}
		return $ajax;
	}
	public static function checkCAPTCHA(){
		if(!isset($_POST['captcha'])) return false;
		require_once('/securimage/securimage.php');
		$cp=new Securimage();
		return ($cp->check($_POST['captcha']));
	}
	public static function JSON($json=false,$exit=false){
		header("Content-type: text/json;charset=utf-8");
		if($exit || $json!==false) exit($json);
	}
	public static function HTML(){
		header("Content-type: text/html;charset=utf-8");
	}
}
$config=Config::load();
?>