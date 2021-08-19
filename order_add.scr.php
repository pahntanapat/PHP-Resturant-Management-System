<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
require 'class.Menu.php';
require 'class.StatFood.php';

if(Config::isAjax(Session::SEC_ORDER,true,true)){
	try{
		$db=$config->PDO();
		$rpn=new SKAjax();
		switch($_POST['table']){
			case Dir::FOOD_ADJ:
				$food=AdjFood::get($_POST['id'],$db);
				break;
			case Dir::FOOD_FIX:
				$food=FixFood::get($_POST['id'],$db);
				break;
			default:
				$rpn->message='ไม่พบประเภทอาหาร';
				Config::JSON($rpn);
		}
		$name=$food->name;
		$price=$food->price;
		$abbr=abbr($food);
		
		$detail=new StatFood();
		$detail->addIngredient(abbr($food,true),$price,$food->getParent()->name);
		
		if($_POST['table']==Dir::FOOD_ADJ){
			$list=array();
			foreach($_POST['ing'] as $v){
				$temp=array();
				foreach($v as $id){
					$list[]=$id;
					$temp[]='[:'.$id.':]';
				}
				$temp=' + '.implode(', ',$temp);
				$name.=$temp;
				$abbr.=$temp;
			}
			$temp=array(IngFood::FOOD_ING,IngDir::DIR_ING);
			$stm=$db->prepare('SELECT '.$temp[0].'.id AS id, '.$temp[0].'.name AS name, '.$temp[0].'.abbr AS abbr, '.$temp[0].'.price AS price, '.$temp[1].'.name AS prn FROM '.$temp[0].' LEFT JOIN '.$temp[1].' ON '.$temp[0].'.parent='.$temp[1].'.id WHERE '.$temp[0].'.id IN ('.implode(',',array_fill(0,count($list),'?')).')');
			$stm->execute($list);
			foreach($stm->fetchAll(PDO::FETCH_OBJ) as $ing){
				$abbr=str_replace('[:'.$ing->id.':]',abbr($ing),$abbr);
				$name=str_replace('[:'.$ing->id.':]',$ing->name,$name);
				$price+=$ing->price;
				$detail->addIngredient(abbr($ing,true),$ing->price,$ing->prn);
			}
			unset($list,$ing,$id,$temp);
		}
		$detail->fullName=$name;
		$detail->abbr=$abbr;
		$detail->eatHere=$_SESSION['order']['here'];
		$detail->amount=$_POST['amount'];
		$detail->note=$_POST['note'];
		$stm=$db->prepare('INSERT INTO `order_list`(`cus_id`, `full_menu`, `abbr_menu`, `note`, `kit_id`, `price`, `amount`, `eat_here`, `detail`) VALUES (:c_id, :n, :ab, :c, :k, :p, :am, :e, :d)');
		$stm->bindValue(':c_id',$_SESSION['order']['id']);
		$stm->bindValue(':n',$name);
		$stm->bindValue(':ab',$abbr);
		$stm->bindValue(':c',$_POST['note']);
		$stm->bindValue(':k',$food->kitchen->id);
		$stm->bindValue(':p',$price);
		$stm->bindValue(':am',$_POST['amount'],PDO::PARAM_INT);
		$stm->bindValue(':e',$_SESSION['order']['here'], PDO::PARAM_BOOL);
		$stm->bindValue(':d',$detail->__toString());
		$rpn->result=$stm->execute();
		$rpn->alert('เพิ่มรายการอาหารเรียบร้อยแล้ว');
		$rpn->addAction(SKAjax::REDIRECT,'order_customer.php?id='.$_SESSION['order']['id']);
	}catch(Exception $e){
		$rpn->result=false;
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
}
?>