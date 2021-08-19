<?php
require_once 'class.Session.php';
require_once 'class.SKAjax.php';
require_once 'config.inc.php';
require_once 'class.Menu.php';

if(Config::isAjax(Session::SEC_MENU)):
	$rpn=new SKAjax();
	try{
		$db=$config->PDO();
		$db->beginTransaction();
		$rpn->result=true;
		switch(@$_REQUEST['act']){
			case "addDir":
				$dir=get($_POST['parent'], $db);
				$rpn->message=$dir->add($_POST['name'])->id;
				break;
			case "delDir":
				$dir=get($_POST['id'],$db,false,true);
		//		$rpn->message=$dir->getParent()->id;
				$dir->del();
				break;
			case "move":
				$dir=get($_POST['id'],$db);
				$dir->prn=$_POST['newprn'];
				$dir->update();
				break;
			case "rename":
				$dir=get($_POST['id'],$db);
				$dir->name=$_POST['name'];
				$dir->update();
				break;
			case "setLimit":
				$dir=IngDir::get($_POST['id'],$db);
				$dir->lim=$_POST['lim'];
				if($dir->update(true))
					$rpn->alert('บันทึกเรียบร้อยแล้ว');
				break;
			case "edit":
				$state=array_sum(@$_POST['st']);
				switch($_POST['table']){
					case Menu::FOOD_ADJ:
						$ing=isset($_POST['ing'])?implode("\n",$_POST['ing']):'';
						if($_POST['id']>0){
							$food=new AdjFood($db ,$_POST['id'] ,$_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $_POST['kitchen'], $state, $ing);
							$state=$food->update(true);
							$rpn->message="แก้ไขอาหารสำเร็จแล้ว จำนวนอาหารที่ถูกแก้ = ".$state;
						}else{
							$state=AdjFood::add($db, $_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $_POST['kitchen'], $state, $ing, false);
							$rpn->message="เพิ่มอาหารสำเร็จแล้ว รหัสใหม่ = ".$state;
						}
						break;
					case Menu::FOOD_FIX:
						if($_POST['id']>0){
							$food=new FixFood($db ,$_POST['id'] ,$_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $_POST['kitchen'], $state);
							$state=$food->update(true);
							$rpn->message="แก้ไขอาหารสำเร็จแล้ว จำนวนอาหารที่ถูกแก้ = ".$state;
						}else{
							$state=FixFood::add($db, $_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $_POST['kitchen'], $state, false);
							$rpn->message="เพิ่มอาหารสำเร็จแล้ว รหัสใหม่ = ".$state;
						}
						break;
					case Menu::FOOD_ING:
						if($_POST['id']>0){
							$food=new IngFood($db ,$_POST['id'] ,$_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $state);
							$state=$food->update(true);
							$rpn->message="แก้ไขส่วนประกอบอาหารสำเร็จแล้ว จำนวนส่วนประกอบอาหารที่ถูกแก้ = ".$state;
						}else{
							$state=IngFood::add($db, $_POST['name'], $_POST['parent'], $_POST['detail'], $_POST['abbr'], $_POST['price'], $state, false);
							$rpn->message="เพิ่มส่วนประกอบอาหารสำเร็จแล้ว รหัสใหม่ = ".$state;
						}
						break;
					default: $rpn->result=false; $rpn->message='';
				}
				if($rpn->message!=''){
					$rpn->addHtmlTextVal(SKAjax::SET_HTML,"#edit","<div class=\"ui-state-error\">".($rpn->message)."</div>");
					$rpn->addHtmlTextVal(SKAjax::SET_HTML,"#ui-tabs-1",'');
					$rpn->message='';
				}
				break;
			case "del":
				switch($_POST['table']){
					case Menu::FOOD_ING:
						$rpn->result=IngFood::get($_POST['id'],$db,true)->del();
						break;
					case Menu::FOOD_FIX:
						$rpn->result=FixFood::get($_POST['id'],$db,true)->del();
						break;
					case Menu::FOOD_ADJ:
						$rpn->result=AdjFood::get($_POST['id'],$db,true)->del();
						break;
					default:
						$rpn->result=false;
				}
				if($rpn->result){
					$rpn->addHtmlTextVal(SKAjax::SET_HTML,'#ui-tabs-1','');
					$rpn->alert("ลบเรียบร้อยแล้ว");
				}
				break;
			default: $rpn->result=false;
		}
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
		$rpn->result=false;
		$rpn->alert($e->__toString());
	}
	Config::JSON($rpn);
elseif(isset($_GET['menu'])):
	$food=$_REQUEST['root']==Menu::ROOT_FOOD;
	$db=$config->PDO();
	$dir=get($_GET['id'],$db);
?>
<h1><?=IngFood::type($dir->tb)?> &quot;<?=$dir->name?>&quot;</h1>
<? 	if(!$food):?>
<form action="menu.scr.php" method="post" id="limForm">
  <div><label for="lim">จำนวนส่วนประกอบในหมวดหมู่นี้ที่ลูกค้าเลือกได้สูงสุด (0 = ไม่จำกัด) :</label>
  <input name="lim" type="number" id="lim" value="<?=$dir->lim?>" min="0" step="1" max="65535" />
  <input name="act" type="hidden" id="act" value="setLimit" />
  <input name="id" type="hidden" id="id" value="<?=$dir->id?>" />
  </div>
  <div><span class="buttonset"><button type="submit">บันทึก</button><button type="reset">ยกเลิก</button></span></div>
</form>
<h3><?=IngFood::type(IngFood::FOOD_ING)?> <span class="buttonset"><a href="#edit" data-table="<?=IngFood::FOOD_ING?>" data-id="0">เพิ่ม</a></span></h3>
<div class="acd">
<?
	$list=$dir->getIng();
	foreach($list as $f):
?>
<h3><?=$f->name?></h3>
<div>
<h2>ชื่อส่วนประกอบ : <?=$f->name?> <span class="buttonset"><a href="#del" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">ลบ</a> <a href="#edit" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">แก้ไข</a></span></h2>
<p><strong>ตัวย่อ</strong> <?=$f->abbr?></p>
<p><strong>รายละเอียด</strong><br /><?=$f->detail?></p>
<p><strong>ราคาบวกเพิ่ม</strong> ฿<?=number_format($f->price,2)?></p>
<p><strong>แบบนั่งกินที่ร้าน (for here) : </strong><? if($f->isState(Food::ST_FOR_HERE)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<strong>แบบกินที่บ้าน (take home) : </strong><? if($f->isState(Food::ST_TAKE_HOME)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<? if($f->isState(Food::ST_FREE_ONLY)):?><strong>แถมเท่านั้น</strong><? endif;?><br />
<? if($f->isState(Food::ST_ONLY_SB)):?><strong>มีเงื่อนไขเฉพาะ (ดูรายละเอียด)</strong><? endif;?></p>
</div>
<? endforeach;?>
</div>
<? else:?><h3><?=FixFood::type(FixFood::FOOD_FIX)?> <span class="buttonset"><a href="#edit" data-table="<?=FixFood::FOOD_FIX?>" data-id="0">เพิ่ม</a></span></h3>
<div class="acd">
<?
	$list=$dir->getFix();
	foreach($list as $f):
?>
<h3><?=$f->name?></h3>
<div>
<h2>ชื่ออาหาร : <?=$f->name?> <span class="buttonset"><a href="#del" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">ลบ</a> <a href="#edit" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">แก้ไข</a></span></h2>
<p><strong>ตัวย่อ</strong> <?=$f->abbr?></p>
<p><strong>รายละเอียด</strong><br /><?=$f->detail?></p>
<p><strong>ราคา</strong> ฿<?=number_format($f->price,2)?></p>
<p><strong>ครัว</strong> <?=$f->kitchen->kitName()?></p>
<p><strong>แบบนั่งกินที่ร้าน (for here) : </strong><? if($f->isState(Food::ST_FOR_HERE)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<strong>แบบกินที่บ้าน (take home) : </strong><? if($f->isState(Food::ST_TAKE_HOME)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<? if($f->isState(Food::ST_FREE_ONLY)):?><strong>แถมเท่านั้น</strong><? endif;?><br />
<? if($f->isState(Food::ST_ONLY_SB)):?><strong>มีเงื่อนไขเฉพาะ (ดูรายละเอียด)</strong><? endif;?></p>
</div>
<? endforeach;?>
</div>
<h3><?=AdjFood::type(AdjFood::FOOD_ADJ)?> <span class="buttonset"><a href="#edit" data-table="<?=AdjFood::FOOD_ADJ?>" data-id="0">เพิ่ม</a></span></h3>
<div class="acd">
<?
	$list=$dir->getAdj();
	foreach($list as $f):
?>
<h3><?=$f->name?></h3>
<div>
<h2>ชื่ออาหาร : <?=$f->name?> <span class="buttonset"><a href="#del" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">ลบ</a> <a href="#edit" data-id="<?=$f->id?>" data-table="<?=$f->tb?>">แก้ไข</a></span></h2>
<p><strong>ตัวย่อ</strong> <?=$f->abbr?></p>
<p><strong>รายละเอียด</strong><br /><?=$f->detail?></p>
<p><strong>ราคา</strong> ฿<?=number_format($f->price,2)?></p>
<p><strong>ครัว</strong> <?=$f->kitchen->kitName()?></p>
<div><strong>ส่วนประกอบ</strong>
<ol><? foreach($f->getIngObj() as $v): ?>
<li>หมวดหมู่ &quot;<?=$v->name?>&quot; (เลือกได้ <?=$v->lim==0?'ไม่จำกัด':($v->lim.' อย่าง')?>)</li>
<? endforeach; ?></ol></div>
<p><strong>แบบนั่งกินที่ร้าน (for here) : </strong><? if($f->isState(Food::ST_FOR_HERE)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<strong>แบบกินที่บ้าน (take home) : </strong><? if($f->isState(Food::ST_TAKE_HOME)):?>มี<? else:?>ไม่มี, หมด<? endif;?><br />
<? if($f->isState(Food::ST_FREE_ONLY)):?><strong>แถมเท่านั้น</strong><? endif;?><br />
<? if($f->isState(Food::ST_ONLY_SB)):?><strong>มีเงื่อนไขเฉพาะ (ดูรายละเอียด)</strong><? endif;?></p>
</div>
<? endforeach;?>
</div>
<? 
	endif;
elseif(isset($_GET['form'])):
	if(@$_GET['id']>0){
		$db=$config->PDO();
		switch($_GET['table']){
			case Menu::FOOD_ADJ:
				$food=AdjFood::get($_GET['id'],$db);break;
			case Menu::FOOD_FIX:
				$food=FixFood::get($_GET['id'],$db);break;
			case Menu::FOOD_ING:
				$food=IngFood::get($_GET['id'],$db);break;
			default: $food=false;
		}
	}else $food=false;
?>
<form action="menu.scr.php" method="post" id="foodForm">
<h2><?=FixFood::type($_GET['table'])?></h2>
<div><label for="name">ชื่อ<?=$_GET['table']==Menu::FOOD_ING?'ส่วนประกอบ':''?>อาหาร : </label>
  <input name="name" type="text" id="name" value="<?=($food)?$food->name:''?>" />
  <input name="id" type="hidden" id="id" value="<?=($food)?$food->id:0?>" />
</div>
<div>
  <label for="abbr">ตัวย่อ : </label>
  <input name="abbr" type="text" id="abbr" value="<?=($food)?$food->abbr:''?>" />
  <input name="table" type="hidden" id="table" value="<?=$_GET['table']?>" />
</div>
<div>
  <label for="detail">รายละเอียด : </label>
  <textarea name="detail" cols="65%" rows="10" id="detail"><?=($food)?$food->detail:''?></textarea>
</div>
<div>
  <label for="price">ราคา :</label>
  <input name="price" type="number" id="price" value="<?=($food)?$food->price:0?>" min="0" max="9999.99" step="0.01" />
  <input name="parent" type="hidden" id="parent" value="<?=($food)?$food->getParent()->id:$_GET['parent']?>" />
<? if($food) if($food->tb!=Menu::FOOD_ING):?>  <br />
  <label for="kitchen">ครัว</label>
  <select name="kitchen" id="kitchen">
  <option value="0" <? if($food->kitchen->id==0):?>selected="selected"<? endif;?>>ยังไม่ตั้งค่า</option>
<? foreach($db->query('SELECT id, name FROM kitchen ORDER BY name') as $kit):?>
  <option value="<?=$kit['id']?>" <? if($food->kitchen->id==$kit['id']):?>selected="selected"<? endif;?>><?=$kit['name']?></option>
<?  endforeach;?>
  </select><? endif;?>
</div>
<div><strong>แบบกินที่นี่ (for here) :</strong> <span class="buttonset">
      <input name="st[0]" type="radio" id="st_00" value="<?=Food::ST_FOR_HERE?>" <?=(($food)?$food->isState(Food::ST_FOR_HERE):false)?"checked=\"checked\"":''?> />
      <label for="st_00">มี</label>
      <input type="radio" name="st[0]" value="0" id="st_01" <?=(($food)?$food->isState(Food::ST_FOR_HERE):false)?'':"checked=\"checked\""?> />
    <label for="st_01">ไม่มี</label>
  </span> <br />
  <strong>แบบกินที่บ้าน (take home) : 
    </strong><span class="buttonset">
      <input name="st[1]" type="radio" id="st_10" value="<?=Food::ST_TAKE_HOME?>" <?=(($food)?$food->isState(Food::ST_TAKE_HOME):false)?"checked=\"checked\"":''?>  />
    <label for="st_10">มี</label>
      <input type="radio" name="st[1]" value="0" id="st_11" <?=(($food)?$food->isState(Food::ST_TAKE_HOME):false)?'':"checked=\"checked\""?> />
      <label for="st_11">ไม่มี</label>
  </span><br />
  <strong>แถมเท่านั้น :</strong> <span class="buttonset">
      <input name="st[2]" type="radio" id="st_20" value="<?=Food::ST_FREE_ONLY?>" <?=(($food)?$food->isState(Food::ST_FREE_ONLY):false)?"checked=\"checked\"":''?> />
      <label for="st_20">ใช่</label>
      <input type="radio" name="st[2]" value="0" id="st_21" <?=(($food)?$food->isState(Food::ST_FREE_ONLY):false)?'':"checked=\"checked\""?> /> <label for="st_21">ไม่</label>
  </span> <br />
  <strong>มีเงื่อนไขเฉพาะ (ในรายละเอียด) :</strong> <span class="buttonset">
      <input name="st[3]" type="radio" id="st_30"  value="<?=Food::ST_ONLY_SB?>" <?=(($food)?$food->isState(Food::ST_ONLY_SB):false)?"checked=\"checked\"":''?> />
   <label for="st_30"> มี</label>
      <input type="radio" name="st[3]" value="0" id="st_31" <?=(($food)?$food->isState(Food::ST_ONLY_SB):false)?'':"checked=\"checked\""?> />
     <label for="st_31"> ไม่มี</label>
  </span>
</div>
<? if($_GET['table']==AdjFood::FOOD_ADJ):?>
<div><strong>หมวดหมู่ส่วนประกอบ :</strong>
<ol id="ingList">
<li><button type="button" id="addIng">เพิ่ม</button></li>
<? if($food) foreach($food->getIngObj() as $ing): ?>
<li><?=$ing->name?> <input name="ing[]" type="hidden" id="ing[]" value="<?=$ing->id?>" /><button type="button" class="delIng">ลบ</button></li>
<? endforeach; ?>
</ol>
</div><? endif;?>
<div class="buttonset"><button type="submit">บันทึก</button><button type="reset">ยกเลิก</button>
  <input name="act" type="hidden" id="act" value="edit" />
</div>
</form>
<?
endif;
?>