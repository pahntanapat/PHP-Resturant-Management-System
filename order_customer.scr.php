<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
require_once 'class.Printing.php';

// Update State + mkdir
$rpn=new SKAjax();
if(@$_POST['act']=='confirm'){
	$sess=new Session();
	if(Config::isAjax(Session::SEC_ORDER,true,true) && $sess->load()->isAuth(Session::AUTH_PIN)){
		$stm=$config->PDO()->prepare('UPDATE order_list SET state=? WHERE id IN ('.implode(',',array_fill(0,count($_POST['id']),'?')).')');
		array_unshift($_POST['id'],Printing::ORDER_CONFIRM);
		$rpn->result=$stm->execute($_POST['id']);
		$rpn->alert('ยืนยัน Order สำเร็จ');
		$sess->setAuth(Session::AUTH_NO);
	}else{
		$rpn->result=false;
		$rpn->message='Need PIN Authenication';
	}
}elseif(@$_POST['act']=='print'){
	if(Config::isAjax(Session::SEC_STOCK|Session::SEC_ORDER)){
		$rpn->message=Printing::printOrder($config->PDO(),1,$_POST['id'],true);
		$rpn->result=true;
		Config::JSON($rpn);		
	}
}

if(Config::isAjax(true)){
	// Create PDF + Print
	$again=0;
	try{
		$db=$config->PDO();
		Printing::printOrder($db);
		$stm=$db->prepare('SELECT COUNT(*) FROM order_list WHERE state=?');
		$stm->bindValue(1,Printing::ORDER_CONFIRM,PDO::PARAM_INT);
		$stm->execute();
		$rpn->addAction(SKAjax::RECALL_SERVER,($stm->fetchColumn()>0));
		unset($stmt,$order,$row,$sql);
	}catch(Exception $e){
		$rpn->alert($e->__toString());
	}
	// Clean SQL + File task
	try{
		// clear working employee
		$rpn->message.="\nClear";
		$stm=$db->prepare('DELETE FROM working WHERE DATE_ADD(start_time, INTERVAL ? HOUR)<NOW()');
		$stm->bindValue(1,$config->EMPLOYEE_WORK_HOUR,PDO::PARAM_INT);
		$stm->execute();
		$rpn->message.="\nworking:".$stm->rowCount();
		// clear remain customer
		$stm=$db->prepare('DELETE FROM order_customer WHERE DATE_ADD(start, INTERVAL ? YEAR)<NOW()');
		$stm->bindValue(1,$config->CLEAR_ORDER,PDO::PARAM_INT);
		$stm->execute();
		$rpn->message.="\ncustomer:".$stm->rowCount();
		$stm=$db->prepare('DELETE FROM order_list WHERE DATE_ADD(time, INTERVAL ? YEAR)<NOW()');
		$stm->bindValue(1,$config->CLEAR_ORDER,PDO::PARAM_INT);
		$stm->execute();
		$rpn->message.="\nmenu:".$stm->rowCount();
		// delete expire member
		$stm=$db->prepare('DELETE FROM member WHERE DATE_ADD(exp_date, INTERVAL ? YEAR)<NOW()');
		$stm->bindValue(1,$config->CLEAR_EXP_MEMBER,PDO::PARAM_INT);
		$stm->execute();
		$rpn->message.="\nmember:".$stm->rowCount();
		// clear old dir
		$f=array(0,0);
		if(is_dir($_SERVER['DOCUMENT_ROOT'].'/'.Printing::FOLDER)){
			foreach(scandir($_SERVER['DOCUMENT_ROOT'].'/'.Printing::FOLDER) as $dir){
				if($dir=='.'||$dir=='..' || !is_dir($dir)) continue;
				if((time()-filemtime($dir))>$config->CLEAR_ORDER*31556952){ //365.2425 d/y *24 h/d *3600 s/h
					if(unlink($dir)) $f[0]++;
					else $f[1]++;
				}
			}
			$rpn->message.="\ndelete dir success:".$f[0]." fail:".$f[1];
		}
		// MogoDB
		unset($stm,$dir,$f);
	}catch(Exception $e){
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
}
?>