<?php
require_once 'class.Session.php';
require_once 'config.inc.php';
if(Config::isAjax(Session::SEC_MEM))
	require_once 'employee_manage.scr.php';

$sess=new Session();
$sess->load()->setAuth(Session::AUTH_NO)->save();
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
label[for='q']{
	display:inline;
	font-weight:bold;
}
</style>
<script src="js/member.js"></script>
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
<div id="title" class="ui-corner-top pageContent"><!-- InstanceBeginEditable name="title" -->ลูกค้าสมาชิก<!-- InstanceEndEditable --></div>
<div class="pageContent" id="content"><!-- InstanceBeginEditable name="content" -->
  <form action="member.scr.php" method="post" name="member_form" id="member_form"><div><span class="buttonset"><button id="checkAll" type="reset">เลือก</button><button id="add" type="button" data-id="0">เพิ่ม</button><button id="del" type="submit">ลบ</button> 
      <a href="member.php" id="refresh">refresh</a></span>
      <input name="act" type="hidden" id="act" value="del">
      &nbsp;<span class="buttonset">
      <input name="show" type="radio" id="show_0" value="all" checked="CHECKED"><label for="show_0">ทั้งหมด</label>
      <input type="radio" name="show" value="false" id="show_1"><label for="show_1">หมดอายุ</label>
      <input type="radio" name="show" value="true" id="show_2"><label for="show_2">ไม่หมดอายุ</label></span>
      &nbsp;<label for="q">ค้นหา: </label><input type="text" name="q" id="q"></div>
  <div id="acd"></div>
  </form><div id="edit"></div>
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
