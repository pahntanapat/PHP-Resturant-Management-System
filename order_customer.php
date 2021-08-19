<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
if(Config::isAjax(Session::SEC_ORDER,true))
	require_once 'order.scr.php';
$sess=new Session();
$sess->load()->setAuth(Session::AUTH_NO)->save();
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/mobile.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>ระบบจัดการร้านอาหาร</title>
<!-- InstanceEndEditable -->
<link href="css/skg.min.css" rel="stylesheet" type="text/css">
<link href="css/jquery.mobile.icons.min.css" rel="stylesheet" type="text/css">
<link href="css/jquery.mobile.structure-1.4.3.min.css" rel="stylesheet" type="text/css">
<link href="css/resturant.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<!--<script type="text/javascript" src="../js/jquery.mobile-1.4.2.min.js"></script>-->
<script src="js/jquery.mobile-1.4.3.min.js"></script>
<script type="text/javascript" src="js/resturant.js"></script>
<script type="text/javascript" src="js/mobile.js"></script>
<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" href="css/order.css"/>
<script src="js/order_customer.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<?
$db=$config->PDO();
if(isset($_GET['id'])):
$sql=<<<SQL
SELECT
	table_no, cus_name, people, start
FROM order_customer
WHERE order_customer.id=? LIMIT 1
SQL;
$stm=$db->prepare($sql);
$stm->execute(array($_GET['id']));
$row=$stm->fetch(PDO::FETCH_OBJ);
?>
<div data-role="page" id="page2">
  <div data-role="header"><a href="order_customer.php" data-role="button" data-icon="back" data-rel="back" data-iconpos="left">เมนู</a>
    <h1>สั่งอาหาร :: <?=$stm->rowCount()>0?(($row->table_no==NULL)?'คุณ '.$row->cus_name:'โต๊ะ '.$row->table_no):'ไม่พบลูกค้า'?></h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
<? if($stm->rowCount()<=0):?>
<b class="center xx-large">ไม่พบโต๊ะหรือลูกค้า</b>
<? else:?>
<div data-role="tabs">
<div data-role="navbar"><ul>
      <li><a href="#main" data-ajax="false" data-icon="grid">ดูรายการที่ยังไม่ยืนยัน</a></li>
      <li><a href="#add" data-ajax="false" data-icon="plus">เพิ่มรายการอาหาร</a></li>
    </ul></div>
    <div id="main" class="ui-body-d ui-content">
    <h1><?=($row->table_no==NULL)?'คุณ '.$row->cus_name:'โต๊ะ '.$row->table_no?></h1>
    <div><strong>จำนวนลูกค้า : </strong><?=$row->people?>&nbsp; &nbsp;<strong> เวลาเปิดโต๊ะ, รับลูกค้า : </strong><?=$row->start?></div>
  <h3 class="ui-bar-a ui-bar ui-corner-all">รายการอาหารที่ยังไม่ยืนยัน</h3>
  <form action="order_customer.scr.php" method="post" id="form_order" data-ajax="false">
  <ol data-role="collapsible-set" data-inset="true">
<?
$sql=<<<SQL
SELECT
	id, full_menu AS full, abbr_menu AS abbr, note, price, amount, time
FROM order_list
WHERE cus_id=? AND state=?
SQL;
$stm=$db->prepare($sql);
$stm->bindValue(1, $_GET['id']);
$stm->bindValue(2, Printing::ORDER_NOT_CONFIRM, PDO::PARAM_INT);
$stm->execute();
while($row=$stm->fetch(PDO::FETCH_OBJ)):
?>
<li data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-inset="false">
    <h4><?=$row->full?><span class="ui-li-count"><?=$row->amount?></span>
      <input name="id[]" type="hidden" id="id[]" value="<?=$row->id?>">
    </h4>
    <div><p><strong><?=$row->full?> (<?=$row->abbr?>)</strong></p>
    <p><?=$row->note?></p>
    <p>฿<?=$row->price?> &times; <?=$row->amount?> = <strong>฿<?=number_format($row->price*$row->amount,2)?></strong> เวลา <?=$row->time?></p>
    <a href="order_customer.php?act=del&id=<?=$row->id?>" data-ajax="false" data-role="button" data-icon="minus" class="delFood">ลบ</a>
    </div>
</li>
<? endwhile; ?>
</ol>
  <button data-icon="check" type="submit">ยืนยันรายการ</button>
  <input name="act" type="hidden" id="act" value="confirm">
  </form>
  </div>
  <div id="add" class="ui-body-d ui-content">
      <h2>เพิ่ม order</h2> 
      <div data-role="controlgroup"><a href="order_add.php?act=prepare&id=<?=$_GET['id']?>&here=1" data-ajax="false" data-role="button" data-icon="star">For here ทานนี่</a><a href="order_add.php?act=prepare&id=<?=$_GET['id']?>&here=0" data-role="button" data-ajax="false" data-icon="home">Take home กลับบ้าน</a></div>
  </div>
</div>
<? endif;?>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<?
else:
?>
<div data-role="page" id="page1">
  <div data-role="header"><a href="order.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">เมนู</a>
    <h1>สั่งอาหาร</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <ul data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="ค้นหา">
<? 
	$stm=$db->query('SELECT * FROM order_customer ORDER BY cus_name, table_no, start, id');
	$row=$stm->fetchAll(PDO::FETCH_NUM);
	$i=0;
?><li data-role="list-divider">โต๊ะ</li><?
	while(isset($row[$i])):
		if($row[$i][1]==NULL) break;
?>
  <li><a href="order_customer.php?id=<?=$row[$i][0]?>"><h3><?=$row[$i][1]?></h3><p>ลูกค้า <?=$row[$i][3]?> คน <?=$row[$i][4]?></p></a></li>
<? $i++;endwhile;?><li data-role="list-divider">ลูกค้า</li>
<?	while(isset($row[$i])):?>
  <li><a href="order_customer.php?id=<?=$row[$i][0]?>"><h3><?=$row[$i][2]?></h3><p>ลูกค้า <?=$row[$i][3]?> คน <?=$row[$i][4]?></p></a></li>
<? $i++;endwhile;?>
  </ul>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<?
endif;
?>
<!-- InstanceEndEditable -->
<div data-role="dialog" id="loginDialog">
  <div data-role="header">
    <h1>กรุณา Log in</h1>
  </div>
  <div data-role="content">
    <form action="login.scr.php" method="post" name="loginForm" id="loginForm" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="phone">Phone:</label>
        <input type="tel" name="phone" id="phone" required />
      </div>
        <div data-role="fieldcontain">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required />
        </div>
        <div class="center"><img src="securimage/securimage_show.php?<?=uniqid()?>" alt="จงตอบคำถามต่อไปนี้" name="captchaIMG" height="96" id="captchaIMG"><br><input name="reload" type="button" id="reload" value="Reload คำถาม" data-icon="refresh">
</div>
    <div data-role="fieldcontain">
      <label for="captcha">คำตอบ = </label>
      <input type="text" name="captcha" id="captcha" required />
    </div>
    <div data-role="controlgroup">
      <input name="Submit" type="submit" id="Submit" value="Log in" data-icon="check" data-iconpos="left" />
      <input name="Reset" type="reset" value="Cancel" data-icon="delete" data-iconpos="left" />
    </div>
    </form>
  </div>
</div>
<div data-role="dialog" id="pinDialog">
  <div data-role="header">
    <h1>กรุณากรอก PIN</h1>
  </div>
  <div data-role="content">
    <form action="login.scr.php" method="post" name="pinForm" id="pinForm" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="pin">PIN:</label>
      <input name="pin" type="password" id="pin" size="16" maxlength="4" required /></div>
        <div data-role="controlgroup" data-type="horizontal">
          <input name="OK" type="submit" id="OK" value="OK" data-icon="check" />
          <input name="cancel" type="reset" id="cancel" value="Cancel" data-icon="delete" />
      </div>
    </form>
  </div>
</div>
</body>
<!-- InstanceEnd --></html>
