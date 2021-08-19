<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';

if(Config::isAjax(Session::SEC_CASH,true,true)){
	
	
}elseif(isset($_POST['old_q'],$_POST['date'],$_GET['_ajax'])){
	//Mongo DB
	echo nl2br(print_r($_REQUEST,true));
}elseif(isset($_REQUEST['cq'])){
	$q=trim($_REQUEST['cq']);
	if(strlen($q)>0){
		$sql=<<<SQL
SELECT
	order_customer.*, SUM(order_list.id) AS cnt,
	ROUND((LENGTH(order_customer.id) - LENGTH(REPLACE(order_customer.id, :q, ""))) / LENGTH(:q)) AS c_id,
	ROUND((LENGTH(order_customer.table_no) - LENGTH(REPLACE(order_customer.table_no, :q, ""))) / LENGTH(:q)) +
	ROUND((LENGTH(order_customer.cus_name) - LENGTH(REPLACE(order_customer.cus_name, :q, ""))) / LENGTH(:q)) AS c_c,
	ROUND((LENGTH(order_customer.start) - LENGTH(REPLACE(order_customer.start, :q, ""))) / LENGTH(:q)) AS c_time
FROM order_customer
LEFT JOIN order_list
ON
	order_customer.id=order_list.cus_id
WHERE
	order_customer.id like :kw OR
	order_customer.table_no like :kw OR
	order_customer.cus_name like :kw OR
	order_customer.start like :kw
GROUP BY order_customer.id
ORDER BY
	c_c DESC, c_time DESC, c_id DESC,
	order_customer.cus_name ASC, order_customer.table_no ASC,
	order_customer.start ASC, order_customer.id ASC
SQL;
		$p=array(':kw'=>'%'.$_REQUEST['cq'].'%',':q'=>$_REQUEST['cq']);
	}else{
		$sql=<<<SQL
SELECT
	order_customer.*, SUM(order_list.id) AS cnt
FROM order_customer
LEFT JOIN order_list
ON
	order_customer.id=order_list.cus_id
GROUP BY order_customer.id
ORDER BY
	order_customer.cus_name ASC, order_customer.table_no ASC,
	order_customer.start ASC, order_customer.id ASC
SQL;
		$p=array();
	}
	$stm=$config->PDO()->prepare($sql);
	$stm->execute($p);
?>
<h4><? if(count($p)>0):?>ผลการค้นหา <b><?=$_REQUEST['cq']?></b> พบ<? endif;?>จำนวนลูกค้า <?=$stm->rowCount()?> ท่าน</h4>
<? if($stm->rowCount()>0) while($row=$stm->fetch(PDO::FETCH_OBJ)):?>
<table width="100%" border="1" cellpadding="3" cellspacing="0">
  <tr>
    <th scope="col">ลูกค้า</th>
    <th scope="col">จำนวนคน</th>
    <th scope="col">เวลาเปิดโต๊ะ</th>
    <th scope="col">เมนู</th>
    <th colspan="2" scope="col">action</th>
  </tr>
  <tr class="center">
    <td><?=strlen($row->cus_name)>0?'คุณ'.$row->cus_name:'โต๊ะ '.$row->table_no?></td>
    <td><?=$row->people?></td>
    <td><?=$row->start?></td>
    <td><?=$row->cnt?></td>
    <td><a href="cashier.scr.php?id=<?=$row->id?>&act=del&ajax=<?=rand()?>" title="ลบลูกค้าที่ยังไม่คิดเงิน" data-title="ลบลูกค้าที่ยังไม่คิดเงิน" class="confirm">ลบ</a></td>
    <td><a href="#?act=cash&id=<?=$row->id?>&ajax=<?=rand()?>" class="cash">คิดเงิน</a></td>
  </tr>
</table>
<?
	endwhile;
	
}
?>