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
<script src="js/order.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<div data-role="page" id="page1">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
    <h1>สั่งอาหาร</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <ul data-role="listview" data-inset="true">
  <li><a href="#page_order">เปิดโต๊ะ, รับลูกค้าใหม่</a></li>
  <li><a href="order_customer.php">สั่งอาหาร</a></li>
  <li><a href="order_list.php">ตรวจสอบ Order</a></li>
  </ul>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<div data-role="page" id="page_order">
  <div data-role="header">
<a href="order.php" data-role="button" data-icon="home" data-iconpos="left">เมนู</a>
    <h1>เปิดโต๊ะ, รับลูกค้าใหม่</h1>
  <a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a> </div>
  <div data-role="content">
    <form action="order.scr.php" method="post" name="new" id="new" data-ajax="false">
      <div data-role="fieldcontain">
        <fieldset data-role="controlgroup">
          <legend>รูปแบบลูกค้า</legend>
          <input name="style" type="radio" id="style_0" value="1" checked="CHECKED" />
          <label for="style_0">นั่งกินที่ร้าน</label>
          <input type="radio" name="style" id="style_1" value="0" />
          <label for="style_1">รอรับอาหาร</label>
        </fieldset>
      </div>
      <div data-role="fieldcontain">
        <label for="table">โต๊ะที่ :</label>
        <input type="text" name="table" id="table" placeholder="กรุณากรอกหมายเลขโต๊ะ" required />
        <input name="act" type="hidden" id="act" value="new">
      </div>
      <div data-role="fieldcontain">
        <label for="people">จำนวนลูกค้า :</label>
        <input type="number" name="people" id="people" value="1" min="1" max="255"  />
      </div>
      <div data-role="controlgroup" data-type="horizontal">
        <button data-icon="check" type="submit">ตกลง</button>
        <button data-icon="delete" type="reset">ยกเลิก</button>
      </div>
    </form>
  </div>
  <div data-role="footer">
    <h4>
      <?=@$sess->empID.': '.@$sess->name?>
    </h4>
  </div>
</div>
<div data-role="dialog" id="dialogOK">
  <div data-role="header">
    <h1>รับลูกค้าใหม่เรียบร้อยแล้ว</h1>
  </div>
  <div data-role="content">
    <p>เปิดโต๊ะหรือรับลูกค้าใหม่เรียบร้อยแล้ว</p>
    <div data-role="controlgroup"><a href="#page1" data-role="button" data-icon="back">กลับไปที่เมนู</a><a href="order_customer.php" data-role="button" data-icon="plus">สั่งอาหาร</a></div>
  </div>
</div>
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
