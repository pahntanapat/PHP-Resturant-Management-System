<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
require_once 'class.Promotion.php';

if(Config::isAjax(Session::SEC_PROM)){
	try{
		$rpn=new SKAjax();
		$db=$config->PDO();
		switch(@$_REQUEST['act']){
			case 'edit':
				$state=array_sum($_POST['st']);
				if($_POST['id']>0){
					$p=new Promotion($_POST['id'],$_POST['name'],$_POST['detail'],$_POST['discount'],$state);
					$rpn->message=$p->update($db,true);
					$rpn->alert('บันทึกข้อมูลแล้ว รหัส = '.$p->id.' จำนวนข้อมูล = '.$rpn->message);
				}else{
					$rpn->message=Promotion::add($db,$_POST['name'],$_POST['detail'],$_POST['discount'],$state);
					$rpn->alert('บันทึกข้อมูลแล้ว รหัส = '.$rpn->message.' จำนวนข้อมูล = 1');
				}
				$rpn->result=true;
				break;
			case 'del':
				$p=new Promotion($_POST['id']);
				$rpn->message=$p->delete($db);
				$rpn->alert('ลบข้อมูลแล้ว รหัส = '.$rpn->message);
				$rpn->result=true;
				break;
		}
	}catch(Exception $e){
		$rpn->result=false;
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
}
?>