<?php
require 'config.inc.php';
require 'class.Session.php';
require 'class.SKAjax.php';
$sess=new Session();
$sess->load()->setAuth(Session::AUTH_NO)->save(); // Clear Auth Value After Processing
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
<script src="js/order_add.js"></script>
<!-- InstanceEndEditable -->
</head>

<body>
<!-- InstanceBeginEditable name="page" -->
<? if(isset($_GET['table'],$_GET['id'])):
	require 'class.Menu.php';
	switch($_GET['table']){
		case Food::FOOD_ADJ:
			$food=AdjFood::get($_GET['id'],$config->PDO());
			break;
		case Food::FOOD_FIX:
			$food=FixFood::get($_GET['id'],$config->PDO());
			break;
		default: $food=false;
	}
?>
<div data-role="page" id="page1">
  <div data-role="header"><a href="order_add.php" data-role="button" data-rel="back" data-icon="back" data-iconpos="left">กลับ</a>
    <h1>สั่งอาหาร &gt;<?=($food)?$food->name:'ไม่พบอาหาร'?></h1>
    <a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a>
  </div>
  <div data-role="content">
<? if($food):?>  
  <h1>ชื่อ : <?=abbr($food,true)?></h1>
  <p><strong>รายละเอียด : </strong>
    <?=$food->detail?><br>
    <strong>ราคา : ฿
    <?=$food->price?>
    </strong><br>
<strong><?='แบบ'.($_SESSION['order']['here']==1?'ทานนี่':'กลับบ้าน').(($_SESSION['order']['here']==1?$food->isState(Food::ST_FOR_HERE):$food->isState(Food::ST_TAKE_HOME))?"มี":"ไม่มี").'ขาย'?></strong></p>
<? if($_SESSION['order']['here']==1?$food->isState(Food::ST_FOR_HERE):$food->isState(Food::ST_TAKE_HOME)):
if($food->kitchen->id==0):?><h2>ไม่ได้ตั้งค่าห้องครัว</h2><? else:?>
<form action="order.scr.php" method="post" id="form1" data-ajax="false">
<? if($food->tb==Menu::FOOD_ADJ) foreach($food->getIngObj(true) as $i=>$ingDir):?>
<div data-role="fieldcontain">
  <fieldset data-role="controlgroup" data-select="<?=$ingDir->lim?>" class="selectMax">
    <legend><?=$ingDir->name?> เลือกได้ <?=$ingDir->lim==0?'ไม่จำกัด':($ingDir->lim.' อย่าง')?></legend>
<? 
$st=$_SESSION['order']['here']?Food::ST_FOR_HERE:Food::ST_TAKE_HOME;
foreach($ingDir->getIng() as $k=>$ing):
	if(!$ing->isState($st)) continue;
	if($ingDir->lim==1):
?>    
    <input type="radio" name="ing[<?=$i?>][]" id="ing<?=$k.'_'.$i?>" value="<?=$ing->id?>" data-price="<?=$ing->price?>" />
<? else:?>
    <input type="checkbox" name="ing[<?=$i?>][]" id="ing<?=$k.'_'.$i?>" class="custom" value="<?=$ing->id?>" data-price="<?=$ing->price?>" />
<? endif;?>
    <label for="ing<?=$k.'_'.$i?>" title="<?=$ing->detail?>"><b><?=abbr($ing,true)?> +฿<?=$ing->price?> :</b> <?=$ing->detail?></label>
<? endforeach;?>
  </fieldset>
</div>
<? endforeach;?>
  <div data-role="fieldcontain">
    <label for="_price">ราคาต่อจาน ฿: </label>
    <input type="text" name="_price" id="_price" value="<?=$food->price?>" readonly  />
    <input name="price" type="hidden" id="price" value="<?=$food->price?>">
  </div>
  <div data-role="fieldcontain">
    <label for="amount">จำนวน @: </label>
    <input type="number" name="amount" id="amount" value="0" min="0" max="65535" step="1" />
    <input name="kit_id" type="hidden" id="kit_id" value="<?=$food->kitchen->id?>">
  </div>
<div data-role="fieldcontain">
  <label for="total">ราคารวม ฿: </label>
  <input name="total" type="text" id="total" value="0.00" readonly />
  <input name="id" type="hidden" id="id" value="<?=$food->id?>">
</div>
<div data-role="fieldcontain">
  <label for="note">หมายเหตุ:</label>
  <textarea cols="40" rows="8" name="note" id="note"></textarea>
</div>
<div data-role="controlgroup" data-type="horizontal">
    <button data-icon="check" type="submit">ตกลง</button>
    <button data-icon="delete" type="reset">ยกเลิก</button>
    <input name="act" type="hidden" id="act" value="add">
    <input name="table" type="hidden" id="table" value="<?=$_GET['table']?>">
</div>
</form>
<? endif;endif;?>
<? endif;?>
  </div>
  <div data-role="footer">
    <h4><?=@$sess->empID.': '.@$sess->name?></h4>
  </div>
</div><? elseif(isset($_GET['id'])):
require_once 'class.Menu.php';
$dir=FoodDir::get($_GET['id'],$config->PDO());
?>
<div data-role="page" id="page2">
  <div data-role="header"><a href="order_add.php" data-role="button" data-rel="back" data-icon="back" data-iconpos="left">กลับ</a>
    <h1>สั่งอาหาร &gt; <?=$dir->name?></h1>
  <a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a> </div>
  <div data-role="content"><? if($dir):?><div><strong>หมวดหมู่ : <?=$dir->name?> (<?=IngDir::type($dir->tb)?>)</strong></div>
  <ul data-role="listview" data-filter="true" data-filter-placeholder="ค้นหา" data-inset="true">
  <li data-role="list-divider"><?=FixFood::type(FixFood::FOOD_FIX)?></li>
<? foreach($dir->getFix() as $food):?>
<li><a href="order_add.php?table=<?=$food->tb?>&id=<?=$food->id?>"><?=$food->name.(strlen($food->abbr)>0?'('.$food->abbr.')':'')?></a></li>
<? endforeach;?>
<li data-role="list-divider"><?=AdjFood::type(AdjFood::FOOD_ADJ)?></li>
<? foreach($dir->getAdj() as $food):?>
<li><a href="order_add.php?table=<?=$food->tb?>&id=<?=$food->id?>"><?=$food->name.(strlen($food->abbr)>0?'('.$food->abbr.')':'')?></a></li>
<? endforeach;?>
</ul><? endif;?>
  </div>
  <div data-role="footer">
    <h4>
      <?=@$sess->empID.': '.@$sess->name?>
    </h4>
  </div>
</div><? else:?>
<div data-role="page" id="page3">
  <div data-role="header"><a href="order_customer.php?id=<?=$_SESSION['order']['id']?>" data-role="button" data-icon="back" data-iconpos="left">กลับ</a>
    <h1>สั่งอาหาร :: เลือกเมนู</h1>
  <a href="logout.php" data-role="button" data-ajax="false" data-icon="delete" data-iconpos="right">log out</a> </div>
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
    <h4>
      <?=@$sess->empID.': '.@$sess->name?>
    </h4>
  </div>
</div>
<? endif;?><!-- InstanceEndEditable -->
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
