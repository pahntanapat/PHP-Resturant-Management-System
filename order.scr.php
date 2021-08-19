<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';

if(Config::isAjax(Session::SEC_ORDER)):
	try{
		$rpn=new SKAjax();
		$db=$config->PDO();
		$db->beginTransaction();
		switch($_POST['act']){
			case 'new':
				$stm=$db->prepare('INSERT INTO order_customer (table_no, cus_name, people) VALUES (:t,:c,:p)');
				if($_POST['style']){
					$stm->bindValue(':t',$_POST['table'],PDO::PARAM_INT);
					$stm->bindValue(':c',NULL,PDO::PARAM_NULL);
				}else{
					$stm->bindValue(':c',$_POST['table'],PDO::PARAM_INT);
					$stm->bindValue(':t',NULL,PDO::PARAM_NULL);
				}
				$stm->bindValue(':p',$_POST['people'],PDO::PARAM_INT);
				$stm->execute();
				$rpn->message=$db->lastInsertId();
				$db->exec('DELETE FROM order_customer WHERE DATE_ADD(start, INTERVAL 1 DAY) < NOW()');
				$rpn->result=true;
				break;
			case 'prepare':
				$_SESSION['order']=$_POST; // id=cus_id, here
				unset($_SESSION['order']['act']);
				$rpn->result=true;
				break;
			case 'del':
				$sess=new Session();
				if($sess->load()->isAuth(Session::AUTH_PIN)){
					$stm=$db->prepare('DELETE FROM order_list WHERE id=?');
					$rpn->result=$stm->execute(array($_POST['id']));
					$sess->setAuth(Session::AUTH_NO)->save();
				}else{
					$rpn->message='Need PIN';
					$rpn->result=false;
				}
				break;
			case 'cancel':
				$sess=new Session();
				if($sess->load()->isAuth(Session::AUTH_PIN)){
					$stm=$db->prepare('SELECT full_menu, note, amount, price, state FROM order_list WHERE id=?');
					$stm->execute(array($_POST['id']));
					$row=$stm->fetch(PDO::FETCH_NUM);
					$stm=$db->prepare('UPDATE order_list SET state=? WHERE id=?');
					$stm->bindValue(1,$row[4]|Printing::ORDER_CANCEL,PDO::PARAM_INT);
					$stm->bindValue(2,$_POST['id']);
					$stm->execute();
					// SAVE action to employee log Food state Reason $_POST['reason']
					
					$rpn->result=true;
				}else{
					$rpn->message='Need PIN';
					$rpn->result=false;
				}
				break;
		}
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
		$rpn->message=$e->__toString();
		$rpn->result=false;
	}
	Config::JSON($rpn);
endif;

?>