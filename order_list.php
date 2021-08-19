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
<script src="js/order_list.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<div data-role="page" id="page1">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
    <h1>รายการอาหารที่สั่งแล้ว</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
    <ul data-role="listview" data-filter="true" data-filter-placeholder="ค้นหา" data-inset="true">
<?
require_once 'class.Printing.php';
$db=$config->PDO();
$stm=$db->prepare('SELECT order_customer.id AS cid, order_list.id AS id, table_no, cus_name, full_menu AS full FROM order_customer INNER JOIN order_list ON  order_customer.id=order_list.cus_id WHERE state=?');
$stm->bindValue(1,Printing::ORDER_CONFIRM|Printing::ORDER_PRINT,PDO::PARAM_INT);
$stm->execute();
foreach($stm->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP) as $cid=>$cus):
?>
    <li data-role="collapsible" data-inset="false" data-collapsed="false">
    <h3><?=$cus[0]['table_no']==NULL?('คุณ '.$cus[0]['cus_name']):('โต๊ะ '.$cus[0]['table_no'])?></h3>
    <ol data-role="listview">
<? foreach($cus as $menu):?>
    <li><a href="<?=Printing::FOLDER.'/'.$cid.'/'.$menu['id'].'.pdf'?>" target="_blank" data-rel="external"><?=$menu['full']?></a><a href="order.scr.php?act=cancel&id=<?=$menu['id']?>" data-icon="minus" data-ajax="false" class="cancel">ลบ</a></li>
<? endforeach;?>
    </ol>
    </li>
<? endforeach;?>
    </ul>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div><!-- InstanceEndEditable -->
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
