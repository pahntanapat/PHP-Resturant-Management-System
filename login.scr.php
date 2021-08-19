<?php
require_once 'config.inc.php';
require_once 'class.Session.php';
require_once 'class.SKAjax.php';
function set(&$data,$df=NULL){
	return isset($data)?$data:$df;
}
function loginOK(&$sess,&$rpn,$noSession=false,$row=array()){
	global $config;
	if(!$noSession){
		$sess->empID=set($row['_id'],-1);
		$sess->name=set($row['name'],$config->DB_USER);
		$sess->permission=set($row['pms'],1<<Session::SEC_EMPY);
		$sess->startWork=set($row['start'],$sess->startWork);
		$sess->workID=set($row['id'],$sess->workID);
	}
	$sess->setAuth(Session::AUTH_LOGIN)->create();
	$rpn->addAction(SKAjax::RESET_FORM);
	return true;
}
function pinOK(&$sess,&$rpn,$noSession=false,$row=array()){
	global $config;
	if(!$noSession){
		$sess->name=set($row['name'],$config->DB_USER);
		$sess->permission=set($row['pms'],1<<Session::SEC_EMPY);
		$sess->startWork=set($row['start'],$sess->startWork);
		$sess->workID=set($row['id'],$sess->workID);
	}
	$sess->setAuth(Session::AUTH_PIN)->save();
	$rpn->addAction(SKAjax::RESET_FORM);
	return true;
}

$sess=new Session();
$sess->load();
$rpn=new SKAjax();
if(isset($_POST['pin'])){
	$rpn->addAction(SKAjax::RESET_FORM);
	if(strlen($_POST['pin'])<4){
		$rpn->alert('กรุณากรอก PIN 4 หลัก');
	}elseif(@$_REQUEST['no_session'] || $sess->checkSession() || $sess->checkCookie()){
		$db=$config->PDO();
		$stm=$db->prepare('SELECT employee.nickname AS name, employee.permission AS pms, working.id AS id, working.start_time AS start FROM employee LEFT JOIN working ON working.emp_id=employee._id WHERE employee._id=?');
		$stm->execute(array($sess->empID));
		if($stm->rowCount()>0){
			$rpn->result=pinOK($sess,$rpn,@$_REQUEST['no_session'],$stm->fetch(PDO::FETCH_ASSOC));
		}elseif($sess->empID==-1){
			$rpn->result=pinOK($sess,$rpn,@$_REQUEST['no_session']);
		}else{
			$sess->setAuth(Session::AUTH_PIN,false)->save();
			$rpn->alert("PIN ไม่ถูกต้อง\n");
			if($sess->isAuth(Session::AUTH_REJECT_PIN) && !@$_REQUEST['no_session']) //ทำลาย session ถ้าใส่ PIN มากกว่าที่กำหนด
				$sess->clear()->save();
		}
	}else{
		$rpn->message.="need log in\n";
	}
}elseif(isset($_POST['captcha'])){
	if(!Config::checkCAPTCHA()){
		$rpn->addAction(SKAjax::RELOAD_CAPTCHA);
		$rpn->addHtmlTextVal(SKAjax::SET_VAL,"#password",'');
		$rpn->alert('คำตอบไม่ถูกต้อง');
	}elseif(strlen($_POST['phone'])==0 || strlen($_POST['password'])==0){
		$rpn->alert('กรุณากรอกเบอร์โทรศัพท์และรหัสผ่าน');
	}else{
		$db=$config->PDO();
		$stm=$db->prepare('SELECT employee._id AS _id, employee.nickname AS name, employee.permission AS pms, working.id AS id, working.start_time AS start FROM employee LEFT JOIN working ON working.emp_id=employee._id WHERE employee.phone=:ph AND employee.password=:pw');
		$stm->bindParam(':ph',$_POST['phone']);
		$stm->bindParam(':pw',$_POST['password']);
		$stm->execute();
		if($stm->rowCount()>0){
			$rpn->result=loginOK($sess,$rpn,@$_REQUEST['no_session'],$stm->fetch(PDO::FETCH_ASSOC));
		}else{
			$stm=$db->prepare('SELECT COUNT(*) FROM employee WHERE (permission>>?)&1=1');
			$stm->bindValue(1,Session::SEC_EMPY,PDO::PARAM_INT);
			$stm->execute();
			if($stm->fetchColumn()==0 && $_POST['phone']==$config->DB_USER && $_POST['password']==$config->DB_PW){
				$rpn->result=loginOK($sess,$rpn,@$_REQUEST['no_session']);
			}else{
				$sess->setAuth(Session::AUTH_LOGIN,false)->save();
				$rpn->alert("ไม่พบเบอร์โทรและรหัสผ่านในระบบกรุณาลองอีกครั้ง\n");
				$rpn->addAction(SKAjax::RELOAD_CAPTCHA);
				$rpn->addHtmlTextVal(SKAjax::SET_VAL,"#password",'');
				$rpn->result=false;
				if($sess->isAuth(Session::AUTH_REJECT_LOGIN)) // Log out ถ้ากรอกผิดมากกว่ากำหนด
					$rpn->addAction(SKAjax::REDIRECT,"logout.php");
			}
		}
	}
}
if(Config::isAjax()){
	Config::JSON($rpn);
}elseif($sess->checkSession()){
	header("Location: ./");
	exit("Logged in");
}
?>