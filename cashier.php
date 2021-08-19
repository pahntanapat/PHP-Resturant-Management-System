<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';

if(Config::isAjax(Session::SEC_CASH,true,true))
	require_once 'cashier.scr.php';
$sess=new Session();
$sess->load()->setAuth(Session::AUTH_NO)->save(); // Clear Auth Value After Processing
?>
<!doctype html>
<html><!-- InstanceBegin template="/Templates/desktop.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta charset="utf-8">
<!-- InstanceBeginEditable name="doctitle" -->
<title>ระบบจัดการร้านอาหาร</title>
<!-- InstanceEndEditable -->
<link href="css/jquery-ui-1.11.0.min.css" rel="stylesheet" type="text/css">
<link href="css/resturant.css" rel="stylesheet" type="text/css">
<link href="css/desktop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.11.0.min.js"></script>
<script type="text/javascript" src="js/resturant.js"></script>
<script type="text/javascript" src="js/desktop.js"></script>
<!-- InstanceBeginEditable name="head" -->
<style>
.inlineForm>*{display:inline-block}
.inlineForm label{font-weight:bold}
</style>
<script src="js/cashier.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<div class="ui-state-default ui-corner-all ui-widget" id="header">
<div class="floatBox ui-corner-all ui-state-default"><?=@$sess->empID.': '.@$sess->name?><br> 
  <a href="logout.php">Log out</a></div>
<div class="xx-large"><a href="index.php"><img src="image/sinkanok-logo.png" alt="logo" width="50" height="50"></a><span class="floatMiddle">Km. 888 Resturant</span></div>
</div>
<div class="center" id="nav">
  <div><a href="index.php">หน้าหลัก</a> <a href="order.php">สั่งอาหาร</a>  <a href="menu.php">แก้ไข menu</a> <a href="stock.php">stock อาหาร</a> <a href="employee_manage.php">จัดการพนักงาน</a> <a href="cashier.php">cashier</a> <a href="member.php">สมาชิกลูกค้า</a></div>
  <div><a href="index.php">ตั้งค่าระบบ</a> <a href="kitchen.php">แก้ไขห้องครัว</a> <a href="working.php">เข้าออกงาน</a> <a href="index.php">ทำบัญชี</a> <a href="index.php">สถิติลูกค้า</a> <a href="index.php">ความเคลื่อนไหวพนักงาน</a> <a href="promotion.php">Promotion</a> <a href="logout.php">Log out</a></div>
</div>
<div id="title" class="ui-corner-top pageContent"><!-- InstanceBeginEditable name="title" -->Cashier<!-- InstanceEndEditable --></div>
<div class="pageContent" id="content"><!-- InstanceBeginEditable name="content" -->
<div id="tabs">
<ul>
<li><a href="#tab-now">ลูกค้าปัจจุบัน</a></li>
<li><a href="#tab-log">ออกใบเสร็จอีกรอบ</a></li>
<li><a href="#tab-com">Other commands</a></li>
</ul>
<div id="tab-now">
  <h2>ลูกค้าปัจจุบัน</h2>
  <div><input name="cq" type="text" id="cq" placeholder="search"><button type="button">Reload</button></div>
  <div id="cusList">Loading</div>
</div>
<div id="tab-log"><h2>ออกใบเสร็จอีกรอบ</h2><div>
  <form action="cashier.scr.php" method="post" name="oldBill" class="inlineForm" id="oldBill">
    <label for="old_q">Search: </label>
    <input type="text" name="old_q" id="old_q" placeholder="search">
    <label for="date">Date: </label>
    <input type="text" name="date" id="date" class="date" placeholder="YYYY-mm-dd" readonly><button type="submit">ค้นหา</button>
  </form>
</div><div id="billList"></div></div>
<div id="tab-com"><h2>Other commands</h2>
  <form action="cashier.scr.php" method="post" name="comm" id="comm"><div class="inlineForm btnset">
        <input name="opr" type="radio" id="opr_0" value="0" checked="CHECKED">
<label for="opr_0">  ตั้งค่า (=)</label>
        <input type="radio" name="opr" value="1" id="opr_1">
      <label for="opr_1">  เพิ่ม (+)</label>
        <input type="radio" name="opr" value="-1" id="opr_2">
        <label for="opr_2"> ลด (-)</label>
  </div><div class="inlineForm"><label for="money">จำนวน: ฿</label>
        <input type="text" name="money" id="money"></div><div class="btnset"><button type="submit">บันทึกจำนวนเงิน</button><button type="reset">ยกเลิก</button><a href="cashier.scr.php?act=open" data-title="เปิดลิ้นชักเก็บเงิน" title="เปิดลิ้นชักเก็บเงิน" class="confirm">เปิดลิ้นชักเก็บเงิน</a></div>
  </form></div>
</div>
<p>Dialog จ่ายเงิน</p>
<p>Dialog ค้นหาสมาชิก</p>
  <p>Dialog สมัคร/แก้ไขสมาชิก</p>
  <p>Dialog เพิ่มโปรโมชั่น</p>
  <p>Dialog พิมพ์ใบเสร็จ เงินทอน</p>
<!-- InstanceEndEditable --></div>
<div class="center pageContent ui-corner-bottom" id="footer">&copy; copyright by <a href="http://sinkanok.com">Sinkanok</a> &amp; SKG Group. All right reserved.</div>
<div id="loginDialog" title="กรุณา Log in" class="dialog">
  <form action="login.scr.php" method="post" name="loginForm" id="loginForm">
      <div data-role="fieldcontain">
        <label for="phone">Phone:</label>
        <input type="tel" name="phone" id="phone" required  />
      </div>
        <div data-role="fieldcontain">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required  />
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
<div id="pinDialog" title="กรุณากรอก PIN" class="dialog"><form action="login.scr.php" method="post" name="pinForm" id="pinForm">
      <div data-role="fieldcontain">
        <label for="pin">PIN:</label>
      <input name="pin" type="password" id="pin" size="16" maxlength="4" required /></div>
        <div data-role="controlgroup" data-type="horizontal">
          <input name="OK" type="submit" id="OK" value="OK" data-icon="check" />
          <input name="cancel" type="reset" id="cancel" value="Cancel" data-icon="check" />
      </div>
</form></div>
</body>
<!-- InstanceEnd --></html>
