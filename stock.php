<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
if(Config::isAjax(Session::SEC_STOCK|Session::SEC_MENU))
	require_once 'stock.scr.php';
$sess=new Session();
$sess->load();
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
<link rel="stylesheet" href="css/food_dir.css" />
<script src="js/food.js"></script>
<script src="js/stock.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<?
if(isset($_GET['table'],$_GET['id'])):
	require 'class.Menu.php';
	switch($_GET['table']){
		case Food::FOOD_ADJ:
			$food=AdjFood::get($_GET['id'],$config->PDO());
			break;
		case Food::FOOD_FIX:
			$food=FixFood::get($_GET['id'],$config->PDO());
			break;
		case Food::FOOD_ING:
			$food=IngFood::get($_GET['id'],$config->PDO());
			break;
		default: $food=false;
	}
?>
<div data-role="page" id="page3">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
        <h1>แก้ไข Stock &gt; <?=$food->name?></h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <form action="stock.scr.php" method="post" id="form1" data-ajax="false">
  <p><strong>ชื่ออาหาร : <?=$food->name?> ตัวย่อ : <?=$food->abbr?>
  </strong>
    <input name="id" type="hidden" id="id" value="<?=$food->id?>">
  </p>
  <p>ราคา : ฿<?=$food->price?>
    <input name="table" type="hidden" id="table" value="<?=$_GET['table']?>">
  </p>
  <p>รายละเอียด : <?=$food->detail?>
    <input name="st[3]" type="hidden" id="st[3]" value="<?=($food->state)&~(Food::ST_FOR_HERE|Food::ST_TAKE_HOME)?>">
  </p>
<!--<div data-role="fieldcontain">-->
  <fieldset data-role="controlgroup" data-type="horizontal">
    <legend>แบบกินที่ร้าน For here</legend>
    <input type="radio" name="st[0]" id="st0_0" value="<?=Food::ST_FOR_HERE?>" <?=($food->isState(Food::ST_FOR_HERE))?'checked="checked"':''?> />
    <label for="st0_0">มี</label>
    <input type="radio" name="st[0]" id="st0_1" value="0"  <?=!($food->isState(Food::ST_FOR_HERE))?'checked="checked"':''?> />
    <label for="st0_1">ไม่มี</label>
  </fieldset>
<!--</div>
<div data-role="fieldcontain">-->
  <fieldset data-role="controlgroup" data-type="horizontal">
    <legend>แบบกินที่บ้าน Take home</legend>
    <input type="radio" name="st[1]" id="st1_0" value="<?=Food::ST_TAKE_HOME?>" <?=($food->isState(Food::ST_TAKE_HOME))?'checked="checked"':''?> />
    <label for="st1_0">มี</label>
    <input type="radio" name="st[1]" id="st1_1" value="0" <?=!($food->isState(Food::ST_TAKE_HOME))?'checked="checked"':''?> />
    <label for="st1_1">ไม่มี</label>
  </fieldset>
<!--</div>-->
<div data-role="controlgroup" data-type="horizontal">
  <button data-icon="check" type="submit">ตกลง</button>
  <button data-icon="delete" type="reset">ยกเลิก</button>
</div></form>
  </div>
  <div data-role="footer"><h4><?=@$sess->empID.': '.@$sess->name?></h4></div>
</div>
<? elseif(isset($_GET['root'],$_GET['id'])):
require_once 'class.Menu.php';
$dir=get($_GET['id'],$config->PDO(),$_GET['root']);
?>
<div data-role="page" id="page2">
  <div data-role="header">
  <a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
        <h1>แก้ไข Stock &gt; <?=$dir->name?></h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <div><strong>หมวดหมู่ : <?=$dir->name?> (<?=IngDir::type($dir->tb)?>)</strong>
  <? if($dir->tb==IngDir::DIR_ING):?><br>สามารถเลือกส่วนประกอบจากหมวดหมู่นี้ได้ <?=($dir->lim>0)?$dir->lim.' อย่าง':'ไม่จำกัด'?> <? endif;?>
  </div>
  <div>
  <ul data-role="listview" data-filter="true" data-filter-placeholder="ค้นหา" data-inset="true">
<? if($dir->tb==IngDir::DIR_ING):?>
<li data-role="list-divider"><?=IngFood::type(IngFood::FOOD_ING)?></li>
<? foreach($dir->getIng() as $food):?>
<li><a href="stock.php?table=<?=$food->tb?>&id=<?=$food->id?>"><?=$food->name.(strlen($food->abbr)>0?'('.$food->abbr.')':'')?></a></li>
<? endforeach;else:?>
<li data-role="list-divider"><?=FixFood::type(FixFood::FOOD_FIX)?></li>
<? foreach($dir->getFix() as $food):?>
<li><a href="stock.php?table=<?=$food->tb?>&id=<?=$food->id?>"><?=$food->name.(strlen($food->abbr)>0?'('.$food->abbr.')':'')?></a></li>
<? endforeach;?>
<li data-role="list-divider"><?=AdjFood::type(AdjFood::FOOD_ADJ)?></li>
<? foreach($dir->getAdj() as $food):?>
<li><a href="stock.php?table=<?=$food->tb?>&id=<?=$food->id?>"><?=$food->name.(strlen($food->abbr)>0?'('.$food->abbr.')':'')?></a></li>
<? endforeach;endif;?>
  </ul>
  </div>
  </div>
  <div data-role="footer">
   <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div>
<? else:?>
<div data-role="page" id="page1">
  <div data-role="header"><a href="index.php" data-role="button" data-ajax="false" data-icon="home" data-iconpos="left">หน้าหลัก</a>
    <h1>แก้ไข Stock</h1><a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
  <div data-role="tabs" id="tabs">
  <div data-role="navbar">
    <ul>
      <li><a href="#one" data-ajax="false">เลือกจากรายการ</a></li>
      <li><a href="#two" data-ajax="false">ค้นหา</a></li>
    </ul>
  </div>
  <div id="one" class="ui-body-d ui-content">
    <div id="dir"></div>
  	<button data-role="button" type="button" id="selectDir">เลือก</button>
  </div>
  <div id="two" class="ui-body-d ui-content">
   <div data-role="fieldcontain"><input type="text" name="q" id="q" title="ค้นหาหมวดหมู่ และรายการอาหาร และส่วนประกอบฯ" placeholder="ค้นหา"></div><div data-role="controlgroup"><button id="sb">ค้นหา</button></div>
   <div id="sr"></div>
  </div>
</div>
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
