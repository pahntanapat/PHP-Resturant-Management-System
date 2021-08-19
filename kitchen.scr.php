<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
require 'class.Printing.php';

$db=$config->PDO();
if(Config::isAjax(Session::SEC_MENU)):
	try{
		$db->beginTransaction();
		$rpn=new SKAjax();
		switch($_REQUEST['act']){
			case 'del':
				$stm=$db->prepare('DELETE FROM kitchen WHERE id=?');
				$stm->execute(array($_POST['id']));
				$rpn->result=true;
				$rpn->alert('ลบครัวแล้ว');
				break;
			case 'save':
				unset($_POST['act']);
				if($_POST[':id']<=0){
					$stm=$db->prepare('INSERT INTO kitchen (name, printer) VALUES (:name,:printer)');
					unset($_POST[':id']);
				}else
					$stm=$db->prepare('UPDATE kitchen SET name=:name, printer=:printer WHERE id=:id');
				$stm->execute($_POST);
				$rpn->addHtmlTextVal(SKAjax::SET_HTML,'#ui-tabs-2',"<b>บันทึกข้อมูลแล้ว รหัส = ".(isset($_POST[':id'])?$_POST[':id']:$db->lastInsertId())." จำนวนข้อมูล = ".$stm->rowCount()."</b>");
				$rpn->result=true;
				break;
		}
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
		$rpn->result=false;
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
elseif(isset($_GET['id'])):
	if($_GET['id']>0){
		$stm=$db->prepare('SELECT * FROM kitchen WHERE id=? LIMIT 1');
		$stm->execute(array($_GET['id']));
		$row=$stm->fetch(PDO::FETCH_ASSOC);
	}else $row=array();
?>
<form action="kitchen.scr.php" method="post" name="form1" id="form1">
  <div><label for=":name">ชื่อครัว : </label>
  <input name=":name" type="text" required id=":name" value="<?=@$row['name']?>">
  <input name=":id" type="hidden" id=":id" value="<?=@$_GET['id']?>">
  </div>
<div><label for=":name">เครื่องพิมพ์ :</label>
  <input name=":printer" type="text" id=":printer" value="<?=@$row['printer']?>">
  <input name="act" type="hidden" id="act" value="save">
</div>
  <ul>
    <li>ถ้าเป็นเครื่องพิมพ์ที่ติดตั้ง driver บน server ในกรอกแค่ชื่อ printer</li>
    <li>ถ้าเป็นเครื่องพิมพ์ใน network ให้กรอก \\{ip หรือ host name ของ คอมพิวเตอร์ที่ลง driver เครื่องพิมพ์นั้น}\{ชื่อ printer} เช่น \\192.168.0.100\Brother DCP-J140W Printer (Copy 1)</li>
    <li>ถ้าต้องการให้ครัวกดพิมพ์เองไม่ต้องกรอก</li>
  </ul>
  <div class="buttonset"><button type="submit">ตกลง</button><button type="reset">ยกเลิก</button></div>
</form>
<? elseif(isset($_GET['print'])):
$sql=<<<SQL
SELECT
	order_list.id, abbr_menu AS abbr, time,
	COALESCE(table_no, cus_name) AS cus,
	kitchen.name, printer
FROM order_list
LEFT JOIN order_customer
ON order_customer.id=order_list.cus_id
LEFT JOIN kitchen
ON order_list.kit_id=kitchen.id
WHERE state=?
ORDER BY order_list.id ASC
SQL;
$stm=$db->prepare($sql);
$stm->bindValue(1,Printing::ORDER_CONFIRM,PDO::PARAM_INT);
$stm->execute();
?>
<table width="100%" border="0">
  <tr>
    <th scope="col">Menu</th>
    <th scope="col">โต๊ะ, ลูกค้า</th>
    <th scope="col">เวลา</th>
    <th scope="col">ครัว</th>
    <th scope="col">เครื่องพิมพ์</th>
    <th scope="col">พิมพ์</th>
  </tr>
<? while($row=$stm->fetch(PDO::FETCH_OBJ)):?>
  <tr>
    <td><?=$row->abbr?></td>
    <td><?=$row->cus?></td>
    <td><?=$row->time?></td>
    <td><?=$row->name?></td>
    <td><?=$row->printer==NULL?'ไม่มี':$row->printer?></td>
    <td><a href="order_customer.scr.php?act=print&id=<?=$row->id?>" class="print"> พิมพ์</a></td>
  </tr>
<? endwhile;?>
</table>
<? else:?>
<table width="100%" border="0">
  <tr>
    <th scope="col">ครัว</th>
    <th scope="col">Printer</th>
    <th scope="col">ลบ|แก้ไข</th>
  </tr>
<?
$stm=$db->query('SELECT * FROM kitchen ORDER BY name, printer, id');
if($stm->rowCount()>0):
while($row=$stm->fetch(PDO::FETCH_NUM)):
?>
  <tr>
    <td><?=$row[1]?></td>
    <td><?=$row[2]?></td>
    <td class="center"><a href="#?act=del&id=<?=$row[0]?>">ลบ</a>|<a href="kitchen.scr.php?id=<?=$row[0]?>" class="edit">แก้ไข</a></td>
  </tr>
<? endwhile;else:?>
  <tr>
    <td colspan="3" class="center xx-large">ไม่พบครัว กรุณาเพิ่มครัว</td>
  </tr>
<? endif;?>
</table>
<? endif;?>