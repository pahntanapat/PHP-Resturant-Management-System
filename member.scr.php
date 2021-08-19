<?php
require_once 'config.inc.php';
require_once 'class.Session.php';
require_once 'class.SKAjax.php';
require_once 'class.Member.php';

if(Config::isAjax(Session::SEC_MEM|Session::SEC_CASH) && count($_POST)>0):
	$sess=new Session();
	$rpn=new SKAjax();
	if($sess->load()->isAuth(Session::AUTH_PIN)){
		try{
			$db=$config->PDO();
			$db->beginTransaction();
			switch(@$_POST['act']){
				case 'renew':
					$stm=$db->prepare('SELECT `exp_date` FROM `member` WHERE id=?');
					$stm->execute(array($_POST['id']));
					$day=$stm->fetchColumn();
					if(time()<strtotime($day.' -'.$config->MEMBER_PRE_RENEW_M.' month')){
						$rpn->message=0;
						$rpn->alert('ยังไม่ให้ต่ออายุ');
						break;
					}elseif(time()>strtotime($day.' +'.$config->MEMBER_POST_RENEW_M.' month')){
						$rpn->message=$config->MEMBER_REGISTER_FEE;
						$day=strtotime('+'.$config->MEMBER_EXP_YEAR.' year',time());
					}else{
						$rpn->message=$config->MEMBER_RENEW_FEE;
						$day=strtotime($day.' +'.$config->MEMBER_EXP_YEAR.' year');
					}
					$day=date('Y-m-d H:i:s',$day);
					$stm=$db->prepare('UPDATE `member` SET `exp_date`=:e WHERE id=:i');
					$stm->bindValue(':e',$day);
					$stm->bindValue(':i',$_POST['id']);
					$rpn->alert('ต่ออายุแล้ว วันหมดอายุ = '.$day);
					break;
				case 'edit':
					if($_POST['id']>0){
						$stm=$db->prepare('UPDATE `member` SET `fname`=:f,`lname`=:l,`phone`=:p, `address`=:a,`score`=:s,`reg_date`=:r,`exp_date`=:e WHERE `_id`=:i');
						$stm->bindValue(':i',$_POST['id']);
					}else{
						$stm=$db->prepare('INSERT INTO `member` (`fname` , `lname` , `phone` , `address` , `score` , `reg_date` , `exp_date`) VALUES (:f, :l, :p, :a, :s, :r, :e)');
					}
					$stm->bindValue(':f',$_POST['fname']);
					$stm->bindValue(':l',$_POST['lname']);
					$stm->bindValue(':p',$_POST['phone']);
					$stm->bindValue(':a',$_POST['address']);
					$stm->bindValue(':s',$_POST['score']);
					$stm->bindValue(':r',$_POST['reg']);
					$stm->bindValue(':e',$_POST['exp']);
					$stm->execute();
					$rpn->alert('บันทึกข้อมูลแล้ว จำนวน '.($stm->rowCount()).' คน รหัส '.($_POST['id']>0?$_POST['id']:$db->lastInsertId()));
					break;
				case 'del':  // No id and isset del[]
					$stm=$db->prepare('DELETE FROM member WHERE _id IN ('.implode(',',array_fill(0,count($_POST['del']),'?')).')');
					$stm->execute($_POST['del']);
					$rpn->alert('ลบสมาชิกแล้ว จำนวน '.($stm->rowCount()).' คน');
					break;
			}
			$rpn->result=true;
			$sess->setAuth(Session::AUTH_NO)->save();
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			$rpn->alert($e->__toString());
		}
	}else{
		$rpn->message="Need PIN";
	}
	$config->JSON();
	exit($rpn);
elseif(isset($_GET['q'])):
	$_GET['q']=trim($_GET['q']);
	$where=$_GET['show']=="true"?'exp_date > NOW()':
		($_GET['show']=="false"?'(exp_date <= NOW())':'1');
	if(strlen($_GET['q'])>0){
		$sql=<<<SQL
SELECT
	*,
 	ROUND((LENGTH(_id) - LENGTH(REPLACE(_id, :q, ""))) / LENGTH(:q)) AS c_id,
	ROUND((LENGTH(fname) - LENGTH(REPLACE(fname, :q, ""))) / LENGTH(:q)) AS c_f,
	ROUND((LENGTH(lname) - LENGTH(REPLACE(lname, :q, ""))) / LENGTH(:q)) AS c_l,
	ROUND((LENGTH(phone) - LENGTH(REPLACE(phone, :q, ""))) / LENGTH(:q)) AS c_ph,
	ROUND((LENGTH(address) - LENGTH(REPLACE(address, :q, ""))) / LENGTH(:q)) AS c_adr
FROM member
WHERE {$where} AND
	(_id like :kw OR
	fname like :kw OR
	lname like :kw OR
	phone like :kw OR
	address like :kw)
ORDER BY c_f+c_l DESC, c_ph DESC, c_adr DESC, c_id DESC, _id ASC
SQL;
		$param=array(':q'=>$_GET['q'],':kw'=>'%'.$_GET['q'].'%');
	}else{
		$param=array();
		$sql='SELECT * FROM member WHERE '.$where.' ORDER BY _id';
	}
	$stm=($config->PDO()->prepare($sql));
	$stm->execute($param);
	while($row=$stm->fetch(PDO::FETCH_OBJ)):
?>
<h3><input name="del[]" type="checkbox" id="del[<?=$row->_id?>]" value="<?=$row->_id?>" data-name="<?=($row->fname).' '.($row->lname)?>" data-address="<?=$row->address?>" data-exp="<?=(strtotime($row->exp_date)<time())?1:0?>">
  <?=$row->_id?> : <?=($row->fname).' '.($row->lname).((strtotime($row->exp_date)>=time())?'':"<b><i>หมดอายุแล้ว</i></b>")?>
</h3>
<div>
  <h2><strong>รหัสลูกค้า :</strong>
<?=$row->_id?> 
  <a href="#edit" data-id="<?=$row->_id?>">แก้ไข</a> <? if(time()>=strtotime($row->exp_date.' -'.$config->MEMBER_PRE_RENEW_M.' month')):?><a href="#renew" data-id="<?=$row->_id?>">ต่ออายุ</a><? endif;?></h2>
  <p><strong>ชื่อ-นามสกุล : </strong><?=($row->fname).' '.($row->lname)?></p>
  <p><strong>ที่อยู่</strong><br>
<?=nl2br($row->address)?></p>
  <p><strong>โทรศัพท์ : </strong><?=$row->phone?></p>
  <!--<p><strong>คะแนนสะสม : </strong><? /*=$row->score*/?></p>-->
  <p><strong>วันสมัครครั้งแรก : </strong><?=$row->reg_date?></p>
  <p><strong>วันหมดอายุ : </strong><?=$row->exp_date?></p>
</div>
<?
	endwhile;
elseif(isset($_GET['id'])):
	$stm=($config->PDO()->prepare('SELECT * FROM member WHERE _id=?'));
	$stm->execute(array($_GET['id']));
	$row=($stm->rowCount()>0)?$stm->fetch(PDO::FETCH_ASSOC):
		array('_id'=>0,'fname'=>'','lname'=>'','address'=>'','phone'=>'','score'=>0,
		'reg_date'=>date("Y-m-d"), 'exp_date'=>date("Y-m-d",strtotime('+'.($config->MEMBER_EXP_YEAR).' year',time())));
?>
<div id="dialog" title="เพิ่ม/แก้ไขลูกค้า">
  <form action="member.scr.php" method="post" name="form" id="form">
    <h2>รหัสลูกค้า : 
      <input name="id" type="hidden" id="id" value="<?=@$row['_id']?>"><?=@$row['_id']?>
    </h2>
    <div data-role="fieldcontain">
      <label><strong>ชื่อ-นามสกุล :</strong></label>
        <input name="fname" type="text" id="fname" value="<?=@$row['fname']?>" required="required">
        <input name="lname" type="text" id="lname" value="<?=@$row['lname']?>">
    </div>
        <div data-role="fieldcontain">
          <label for="address">ที่อยู่ :  </label>
            <textarea name="address" cols="50%" rows="10" id="address" required="required"><?=@$row['address']?></textarea>
    </div><div data-role="fieldcontain">
      <label for="phone">โทรศัพท์ : </label>
      <input name="phone" type="tel" id="phone" value="<?=@$row['phone']?>">
    </div><!--<div data-role="fieldcontain">
      <label for="score">คะแนนสะสม : </label>
      <input name="score" type="number" id="score" min="0" step="0.0001" value="<? /*=@$row['score']*/?>" required="required">
    </div>--><div data-role="fieldcontain">
      <label for="reg">วันที่สมัครครั้งแรก : </label>
      <input name="reg" type="text" class="date" id="reg" value="<?=@$row['reg_date']?>" required="required" readonly="readonly">
    </div><div data-role="fieldcontain">
      <label for="exp">วันหมดอายุ : </label>
      <input name="exp" type="text" class="date" id="exp" value="<?=@$row['exp_date']?>" required="required" readonly="readonly">
    </div>
    <div data-role="controlgroup" class="buttonset"><button type="submit">บันทึก</button><button type="reset">ยกเลิก</button>
      <input name="act" type="hidden" id="act" value="edit" />
    </div>
  </form>
</div>
<? endif; ?>