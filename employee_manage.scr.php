<?php
require_once 'config.inc.php';
require_once 'class.Session.php';
require_once 'class.SKAjax.php';

if(Config::isAjax(Session::SEC_EMPY|Session::SEC_TIME)):
	$db=$config->PDO();
	$sess=new Session();
	$sess->load();
	$rpn=new SKAjax();
	if($sess->isAuth(Session::AUTH_PIN)){
		try{
			$db->beginTransaction();
			if(isset($_POST[':_id'])){
				if($_POST[':_id']<=0){
					$sql='INSERT INTO `employee` (`nickname`, `phone`, `password`, `pin`, `permission`) VALUES (:nickname, :phone, :password, :pin, :pms)';
					unset($_POST[':_id']);
				}else
					$sql='UPDATE `employee` SET `nickname`=:nickname, `phone`=:phone, `password`=:password, `pin`=:pin, `permission`=:pms WHERE _id=:_id';
				$stm=$db->prepare($sql);
				$_POST[':pms']=array_sum($_POST['pms']);
				unset($_POST['pms']);
				$stm->execute($_POST);
				$rpn->alert("บันทึกข้อมูลพนักงาน รหัส = ".(isset($_POST[':_id'])?$_POST[':_id']:$db->lastInsertId())." แล้ว");
			}elseif(isset($_POST['del'])){
				$sql='DELETE FROM ';
				if($_GET['act']=='del')	$sql.='employee WHERE ';
				else	$sql.='working WHERE emp';
				$sql.='_id IN ('.implode(',',array_fill(0,count($_POST['del']),'?')).')';
				$stm=$db->prepare($sql);
				$stm->execute($_POST['del']);
				$rpn->alert("ลบพนักงาน {$stm->rowCount()} คนแล้ว");
			}
			$rpn->result=true;
			$sess->setAuth(Session::AUTH_NO)->save();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			$rpn->alert("$e\n\n$sql");
			$rpn->result=false;
		}
	}else{
		$rpn->message=SKAjax::PIN_DIALOG;
	}
	
	Config::JSON();
	echo $rpn;
elseif(isset($_GET['id'])):
	$db=$config->PDO();
	$sql=<<<SQL
SELECT
	nickname, phone, permission, password, pin
FROM employee
WHERE _id=?
SQL;
	$stm=$db->prepare($sql);
	$stm->bindValue(1,$_GET['id']);
	$stm->execute();
	$row=($stm->rowCount()>0)?$stm->fetch(PDO::FETCH_NUM):array('','','','','');
	Config::HTML();
?>
<div title="แก้ไข/เพิ่มพนักงาน" id="editDialog">
  <form action="employee_manage.php" method="post" id="form1">
    <div><strong>รหัสพนักงาน: </strong><span id="id_span">-</span>
      <input name=":_id" type="hidden" id=":_id" value="<?=$_GET['id']?>">
    </div>
    <div data-role="fieldcontain">
      <label for=":nickname">ชื่อเล่น</label>
      <input name=":nickname" type="text" id=":nickname" value="<?=$row[0]?>">
    </div>
    <div data-role="fieldcontain">
      <label for=":phone">เบอร์โทรศัพท์</label>
      <input name=":phone" type="tel" id=":phone" value="<?=$row[1]?>">
    </div>
    <div data-role="fieldcontain">
      <label for=":password">รหัสผ่าน</label>
      <input name=":password" type="text" id=":password" value="<?=$row[3]?>">
    </div>
    <div data-role="fieldcontain">
      <label for=":pin">PIN</label>
      <input name=":pin" type="text" id=":pin" value="<?=$row[4]?>">
    </div>
    <div data-role="fieldcontain"><h3>พนักงานคนนี้สามารถเข้าถึงระบบ...</h3>
<ol>
<?
$ref=new ReflectionClass('Session');
foreach($ref->getConstants() as $k=>$v):
if(strpos($k,'SEC_')===false) continue;
?><li><label>
<input name="pms[]" type="checkbox" value="<?=$v?>"<?=Session::permission($row[2],$v)?' checked':''?>><?=Session::section($v);?></label>
  </li><? endforeach;?>
</ol>
      </div>
    <div data-role="controlgroup"><button type="submit">บันทึก</button><button type="reset">ยกเลิก</button></div>
</form></div>
<?
elseif(isset($_GET['q'])):
	$db=$config->PDO();
	$_GET['q']=trim($_GET['q']);
	if(strlen($_GET['q'])>0){
		$sql=<<<SQL
SELECT
	employee._id AS _id, employee.nickname AS name,
	employee.phone AS phone, employee.permission AS pms,
	working.start_time AS start,
	ROUND((LENGTH(_id) - LENGTH(REPLACE(_id, :q, ""))) / LENGTH(:q)) AS c_id,
	ROUND((LENGTH(employee.nickname) - LENGTH(REPLACE(employee.nickname, :q, ""))) / LENGTH(:q)) AS c_name,
	ROUND((LENGTH(phone) - LENGTH(REPLACE(phone, :q, ""))) / LENGTH(:q)) AS c_phone -- ,
	-- ROUND((LENGTH(working.start_time) - LENGTH(REPLACE(working.start_time, :q, ""))) / LENGTH(:q)) AS c_start
FROM	employee
LEFT JOIN	working
ON	working.emp_id=employee._id
WHERE
	employee._id like :kw OR employee.nickname like :kw OR phone like :kw -- OR working.start_time like :kw
ORDER BY
	c_name DESC, c_phone DESC, c_id DESC, -- c_start DESC, 
	name ASC, id ASC
SQL;
		$stm=$db->prepare($sql);
		$stm->bindValue(':q',$_GET['q']);
		$stm->bindValue(':kw','%'.$_GET['q'].'%');
	}else{
		$sql=<<<SQL
SELECT
	employee._id AS _id, employee.nickname AS name,
	employee.phone AS phone, employee.permission AS pms,
	working.start_time AS start
FROM	employee
LEFT JOIN	working
ON	working.emp_id=employee._id
ORDER BY _id
SQL;
		$stm=$db->prepare($sql);
	}
	$stm->execute();
	Config::HTML();
?>
<div id="accordion">
<? while($row=$stm->fetch(PDO::FETCH_OBJ)):?>
<h3><input name="del[]" type="checkbox" id="del[<?=$row->_id?>]" value="<?=$row->_id?>"> <?="{$row->_id}: {$row->name}"?></h3>
<div>
  <h2>ข้อมูลพนักงาน <a href="#edit" data-id="<?=$row->_id?>">แก้ไข</a></h2>
  <p><strong>รหัสพนักงาน :</strong><span id="id_span"> <?=$row->_id?></span></p>
  <p><strong>ชื่อเล่น : </strong><?=$row->name?></p>
  <p><strong>เบอร์โทรศัพท์ : </strong><?=$row->phone?></p>
  <h3>พนักงานคนนี้สามารถเข้าถึงระบบ...</h3>
<ol><?
$ref=new ReflectionClass('Session');
foreach($ref->getConstants() as $k=>$v):
if(strpos($k,'SEC_')===false) continue;
?><li><?=Session::permission($row->pms,$v)?"&#x2714;":"&#x2718;"?><?=Session::section($v);?></li><? endforeach;?></ol>
</div><? endwhile;?>
</div><? endif;?>