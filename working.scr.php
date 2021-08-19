<?php
require_once 'config.inc.php';
require_once 'class.Session.php';
require_once 'class.SKAjax.php';
if(Config::isAjax(Session::SEC_TIME,false,true)){
	$sess=new Session();
	$sess->load();
	$rpn=new SKAjax();
	try{
		$db=$config->PDO();
		$db->beginTransaction();
		if($_GET['act']=='out')
			if($sess->isAuth(Session::AUTH_PIN)){
				$stm=$db->prepare('DELETE working.* FROM working INNER JOIN employee ON employee._id=working.emp_id WHERE (employee.pin=:pin AND employee.phone=:ph) OR TIME_TO_SEC(TIMEDIFF(NOW(),working.start_time))>=:max');
				$stm->bindValue(':pin',$_POST['pin']);
				$stm->bindValue(':ph',$_POST['ph']);
				$stm->bindValue(':max',$config->EMPLOYEE_WORK_HOUR,PDO::PARAM_INT);
				$stm->execute();
				$rpn->alert('ออกจากงานแล้ว (@'.($stm->rowCount()).')');
				$rpn->addHtmlTextVal(SKAjax::SET_VAL,'#pin','');
				$sess->setAuth(Session::AUTH_NO)->save();
				$rpn->result=true;
			}else{
				$rpn->message="Please input PIN";
			}
		elseif($_GET['act']=='in')
			if($sess->isAuth(Session::AUTH_LOGIN)){
				$stm=$db->prepare('DELETE working.* FROM working INNER JOIN employee ON employee._id=working.emp_id WHERE (phone=? AND password=?) OR TIME_TO_SEC(TIMEDIFF(NOW(),working.start_time))>=?');
				$stm->bindValue(1,@$_POST['phone']);
				$stm->bindValue(2,@$_POST['password']);
				$stm->bindValue(3,$config->EMPLOYEE_WORK_HOUR,PDO::PARAM_INT);
				$stm->execute();
				$stm=$db->prepare('INSERT INTO working (emp_id) (SELECT _id FROM employee WHERE phone=? AND password=?)');
				$stm->execute(array(@$_POST['phone'],@$_POST['password']));
				$rpn->alert("เข้างานเสร็จแล้ว (#{$db->lastInsertId()}@{$stm->rowCount()})");
				$sess->setAuth(Session::AUTH_NO)->save();
				$rpn->result=true;
			}else{
				$rpn->message="Please input log in";
			}
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
}
?>