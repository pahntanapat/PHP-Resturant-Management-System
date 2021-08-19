<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
require_once 'class.Promotion.php';

if(Config::isAjax(Session::SEC_PROM))
	require_once 'promotion.scr.php';
$sess=new Session();
$sess->setAuth(Session::AUTH_NO); // Clear Auth Value After Processing
$db=$config->PDO();
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
<script src="js/promotion.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<? if(isset($_GET['id'])):
$p=Promotion::load($db,$_GET['id']);
?>
<div data-role="page" id="page2">
  <div data-role="header">
    <a href="promotion.php" data-role="button" data-icon="back" data-rel="back">กลับ</a>
    <h1>Promotion</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
    <form action="promotion.scr.php" method="post" name="form" id="form" data-ajax="false">
      <div data-role="fieldcontain">
        <label for="name">ชื่อ :</label>
        <input type="text" name="name" id="name" value="<?=$p->name?>" required />
        <input name="id" type="hidden" id="id" value="<?=$p->id?>">
      </div>
  <div data-role="fieldcontain">
    <label for="detail">รายละเอียด :</label>
    <textarea cols="40" rows="8" name="detail" id="detail"><?=$p->detail?></textarea>
  </div>
  <div data-role="fieldcontain">
    <label for="discount">ส่วนลด :</label>
    <input type="number" min="0" step="0.01" name="discount" id="discount" value="<?=$p->discount?>" required />
  </div>
  <div data-role="fieldcontain">
    <label for="st[0]">หน่วย (จากราคาเต็ม) :</label>
    <select name="st[0]" id="st[0]" data-role="slider">
      <option value="0"<? if(!$p->is(Promotion::DISCOUNT_PERCENT)):?> selected="selected"<? endif;?>>฿ บาท </option>
      <option value="<?=Promotion::DISCOUNT_PERCENT?>"<? if($p->is(Promotion::DISCOUNT_PERCENT)):?> selected="selected"<? endif;?>>% ร้อยละ </option>
    </select></div>
  <div data-role="fieldcontain">
    <fieldset data-role="controlgroup" data-type="horizontal">
      <legend>เปิดใช้งาน : </legend>
      <input type="radio" name="st[1]" id="st1_0" value="0" <?=$p->is(Promotion::IS_ACTIVE)?'':'checked'?> />
      <label for="st1_0">ไม่</label>
      <input type="radio" name="st[1]" id="st1_1" value="<?=Promotion::IS_ACTIVE?>" <?=$p->is(Promotion::IS_ACTIVE)?'checked':''?> />
      <label for="st1_1">ใช่</label>
    </fieldset>
  </div>
  <div data-role="fieldcontain">
    <fieldset data-role="controlgroup">
      <legend>อื่นๆ : </legend>
      <input type="checkbox" name="st[2]" id="st2_0" class="custom" value="<?=Promotion::ONLY_MEMBER?>" <?=$p->is(Promotion::ONLY_MEMBER)?'checked':''?> />
      <label for="st2_0">เฉพาะสมาชิกร้านเท่านั้น</label>
      <input name="act" type="hidden" id="act" value="edit">
    </fieldset>
  </div>
  <div data-role="controlgroup" data-type="horizontal">
    <button data-icon="check" type="submit">บันทึก</button>
    <button data-icon="delete" type="reset">ยกเลิก</button>
  </div>
    </form>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<? else:?>
<div data-role="page" id="page1">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
    <h1>Promotion</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <a href="promotion.php?id=0" data-role="button" data-icon="plus">เพิ่มโปรโมชั่น</a>
  <ol data-role="listview" data-filter="true" data-filter-placeholder="ค้นหา">
<? foreach(Promotion::getList($db) as $p): ?>
  <li><a href="promotion.php?id=<?=$p->id?>"><h3><?=$p->name?> = <?=$p?></h3><h5><?=$p->detail?></h5></a><a class="del" href="promotion.scr.php?act=del&id=<?=$p->id?>" data-ajax="false" data-icon="minus">ลบ</a></li>
<? endforeach;?>
  </ol>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<? endif;?>
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
