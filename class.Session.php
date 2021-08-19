<?php
require_once 'config.inc.php';
class Session{
	private $_ip,$auth=0;
	public $startWork=0,$workID=0,$empID,$name,$permission=0;
	const
		AUTH_PIN=1,
		AUTH_LOGIN=2,
		AUTH_NO=0,
		AUTH_REJECT_PIN=-3,
		AUTH_REJECT_LOGIN=-10,
		
		SEC_ORDER=1,
		SEC_MENU=2,
		SEC_EMPY=4,
		SEC_CASH=8,
		SEC_MEM=16,
		SEC_ACC=32,
		SEC_STAT=64,
		SEC_LOG=128,
		SEC_PROM=256,
		SEC_TIME=512,
		SEC_STOCK=1024;
	public function __construct(){
		$this->_ip=self::sessionStart()?$_SERVER['REMOTE_ADDR']:'';
	}
	public function __destruct(){
		unset($this->_ip,$this->startWork,$this->workID,$this->empID,$this->auth);
	}
	public static function sessionStart(){
		switch(session_status()){
			case PHP_SESSION_NONE:			return session_start();
			case PHP_SESSION_ACTIVE:		return true;
			case PHP_SESSION_DISABLED:	return false;
		}
	}
	public function checkCookie(){
		return isset($_COOKIE['startWork'],$_COOKIE['workID'])?
		($_COOKIE['startWork']==$this->startWork && $_COOKIE['workID']==$this->workID)
		&& !($_COOKIE['startWork']==0 || $_COOKIE['workID']==0)
		:false;
	}
	public function checkSession(){
		return (	$this->_ip==$_SERVER['REMOTE_ADDR'] && $this->empID!=NULL);
		//	$_COOKIE['startWork']==$this->startWork &&
		//	$_COOKIE['workID']==$this->workID &&
	}
	public function loadCookie($db){ // ดึงข้อมูลจาก cookie ถ้า session หมดอายุ
		$stm=$db->prepare('SELECT COUNT(*) FROM working WHERE id=:id AND start_time=:st');
		$stm->bindValue(':id',$_COOKIE['workID']);
		$stm->bindValue(':st',$_COOKIE['startWork']);
		$stm->execute();
		if($stm->fetchColumn()>0){
			$this->startWork=$_COOKIE['startWork'];
			$this->workID=$_COOKIE['workID'];
			return true;
		}
		return false;
	}
	public function load(){
		if(isset($_SESSION['SKSession'])){
			foreach($this as $k=>$v)
				$this->$k=$_SESSION['SKSession'][$k];
		}else{
			foreach(get_class_vars(__CLASS__) as $k=>$v)
				$this->$k=$v;
		}
		return $this;
	}
	public function save(){
		$_SESSION['SKSession']=get_object_vars($this);
		$_SESSION['SKSession']['_ip']=$_SERVER['REMOTE_ADDR'];
		$this->_ip=$_SERVER['REMOTE_ADDR'];
		return $this;
	}
	public function create(){
		setcookie('workID',$this->workID,time()+3600*$GLOBALS['config']->EMPLOYEE_WORK_HOUR);
		setcookie('startWork',$this->startWork,time()+3600*$GLOBALS['config']->EMPLOYEE_WORK_HOUR);
		return $this->save();
	}
	public static function destroy(){
		self::sessionStart();
		unset($_SESSION['SKSession']);
		setcookie('workID',NULL,1);
		setcookie('startWork',NULL,1);
		session_destroy();
	}
	public function clear(){
		unset($_SESSION['SKSession']);
		return $this;
	}
	public function isAuth($authType){
		return ($authType<self::AUTH_NO)?($this->auth<=$authType):($this->auth>=$authType);
	}
	public function setAuth($authType,$isPass=true){
	//	switch($isPass){
	//		case true:	$this->auth+=$authType;break;
	//		case false:	$this->auth-=$authType;break;
	//		default:			$this->auth=$authType;
	//	}
		$this->auth=($isPass)?$authType:$this->auth-$authType;
		return $this;
	}
	public static function permission($perms,$section){
		return ($perms&$section)!=0;
	}
	public function perms($section){
		return self::permission($this->permission,$section);
	}
	public static function section($section){
		switch($section){
			case self::SEC_ACC: return "บัญชีร้าน";
			case self::SEC_CASH: return "Cashier";
			case self::SEC_EMPY: return "พนักงาน";
			case self::SEC_LOG: return "ความเคลื่อนไหวพนักงาน";
			case self::SEC_MEM: return "ระบบสมาชิกลูกค้า";
			case self::SEC_MENU: return "เมนูอาหาร";
			case self::SEC_ORDER: return "สั่งอาหาร";
			case self::SEC_PROM: return "Promotion และ ส่วนลด";
			case self::SEC_STAT: return "สถิติลูกค้า";
			case self::SEC_TIME: return "เข้าออกงาน";
			case self::SEC_STOCK: return "stock อาหาร";
			default: return "ไม่พบส่วนนี้";
		}
	}
}
?>