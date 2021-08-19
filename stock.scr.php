<?php
require_once 'class.Session.php';
require_once 'class.SKAjax.php';
require_once 'config.inc.php';
require_once 'class.Menu.php';

if(Config::isAjax(Session::SEC_STOCK|Session::SEC_MENU)):
	try{
		$rpn=new SKAjax();
		switch($_POST['table']){
			case Food::FOOD_ADJ:
				$food=AdjFood::get($_POST['id'],$config->PDO(),true);
				break;
			case Food::FOOD_FIX:
				$food=FixFood::get($_POST['id'],$config->PDO(),true);
				break;
			case Food::FOOD_ING:
				$food=IngFood::get($_POST['id'],$config->PDO(),true);
				break;
			default: $food=false;
		}
		if($food){
			$food->state=array_sum($_POST['st']);
			$rpn->message=$food->updateState(true);
			$rpn->result=true;
			$rpn->alert( "บันทึกข้อมูลแล้ว รายการ : ".$rpn->message);
		}
	}catch(Exception $e){
		$rpn->result=false;
		$rpn->alert( $e->__toString());
	}
	Config::JSON($rpn);
elseif(isset($_GET['parent'],$_REQUEST['root'])):
	if($_GET['parent']<0):
		if(strpos($_GET['root'],Menu::ROOT_FOOD)!==false):
?><ul class="<?=Menu::ROOT_FOOD?> dir"><li><input name="id" type="radio" id="<?=Menu::ROOT_FOOD?>0" value="0" checked><label for="<?=Menu::ROOT_FOOD?>0">หมวดหมู่อาหารทั้งหมด</label><!--? $_GET['parent']=0; $_GET['add']=rand() ;$_REQUEST['root']=Menu::ROOT_FOOD;require("stock.scr.php")?--></li></ul><? endif;if(strpos($_GET['root'],Menu::ROOT_ING)!==false): ?><ul class="<?=Menu::ROOT_ING?> dir"><li><input name="id" type="radio" id="<?=Menu::ROOT_ING?>0" value="0"><label for="<?=Menu::ROOT_ING?>0">หมวดหมู่ส่วนประกอบอาหารทั้งหมด</label><!--? $_REQUEST['root']=Menu::ROOT_ING; require("stock.scr.php")?--></li></ul>
<? endif; else: 	$ul=get($_GET['parent'], $config->PDO()); ?>
<ul><? foreach($ul->getChildren() as $li): ?>
<li><input name="id" type="radio" id="<?=$_REQUEST['root'].($li->id)?>" value="<?=$li->id?>"><label for="<?=$_REQUEST['root'].($li->id)?>" title="คลิกเพื่อแสดงหมวดหมู่ย่อย"><?=$li->name?></label></li>
<? endforeach; if(isset($_GET['add'])): ?>
<li><input name="add" type="text" title="เพิ่มหมวดหมู่ใหม่ (พิมพ์แล้วกด enter)" placeholder="เพิ่มหมวดหมู่"> (พิมพ์แล้วกด enter)</li></ul>
<?		endif;
	endif;
elseif(isset($_GET['q'])):
	try{
		$sql=array();
		$sql[0]=<<<SQL
id, name, NULL AS abbr, NULL AS detail, parent AS prn,
ROUND(LOG(65535)*(LENGTH(name) - LENGTH(REPLACE(name, :q, ""))) / (LOG(30)*LENGTH(:q))) AS od
SQL;
		$sql[1]=<<<SQL
id, name, abbr, detail, parent AS prn,
ROUND(LOG(65535)*(LENGTH(name) - LENGTH(REPLACE(name, :q, ""))) / (LOG(30)*LENGTH(:q)))+
ROUND(LOG10(65535)*(LENGTH(abbr) - LENGTH(REPLACE(abbr, :q, ""))) / LENGTH(:q))+
ROUND((LENGTH(detail) - LENGTH(REPLACE(detail, :q, ""))) / LENGTH(:q)) AS od
SQL;
		$sql[2]='(SELECT %1$s, \'%2$s\' AS tb, \'%3$s\' AS root FROM %2$s WHERE name like :kw)';
		$sql[3]='(SELECT %1$s, \'%2$s\' AS tb, \'%3$s\' AS root FROM %2$s WHERE name like :kw OR abbr like :kw OR detail like :kw)';
		$exc=array();
		if($_GET['AdjFood']==1) $exc[]=sprintf($sql[3],$sql[1],Menu::FOOD_ADJ,Menu::ROOT_FOOD);
		if($_GET['FixFood']==1) $exc[]=sprintf($sql[3],$sql[1],Menu::FOOD_FIX,Menu::ROOT_FOOD);
		if($_GET['IngFood']==1) $exc[]=sprintf($sql[3],$sql[1],Menu::FOOD_ING,Menu::ROOT_ING);
		if($_GET['FoodDir']==1) $exc[]=sprintf($sql[2],$sql[0],Menu::DIR_FOOD,Menu::ROOT_FOOD);
		if($_GET['IngDir']==1) $exc[]=sprintf($sql[2],$sql[0],Menu::DIR_ING,Menu::ROOT_ING);
		$sql='SELECT * FROM ('.implode(' UNION ',$exc).') AS t ORDER BY od DESC, name ASC, id ASC';
		
		$stm=$config->PDO()->prepare($sql);
		$stm->bindValue(':q',trim($_GET['q']));
		$stm->bindValue(':kw','%'.trim($_GET['q']).'%');
		$stm->execute();
?>
<p><b>พบ <?=$stm->rowCount()?> ผลการค้นหา</b></p><ol data-role="listview" data-inset="true">
<? while($row=$stm->fetch(PDO::FETCH_OBJ)):?>
<li>
<a href="<?=$_GET['linkTo'].'?id='.($row->id)."&table=".($row->tb)."&parent=".($row->prn)."&root=".($row->root)?>" data-id="<?=$row->id?>" data-table="<?=$row->tb?>" data-parent="<?=$row->prn?>" data-root="<?=$row->root?>"><?=$row->name?><?=($row->abbr!='')?'('.($row->abbr).')':''?></a>
<div><b><?=IngFood::type($row->tb)?></b><br /><?=$row->detail?><br/>&nbsp;</div>
</li>
<? endwhile; ?></ol>
<?	}catch(Exception $e){
		echo "<pre>$e\n\n$sql</pre>";
	}
endif;
?>