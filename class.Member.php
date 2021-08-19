<?php
class Member{
	public $_id=0, $fname='', $lname='', $phone='', $address='', $reg_date='',$exp_date='';
	public static function get(PDO $db,$_id){
		if($_id<0) return new self();
		$stm=$db->prepare('SELECT * FROM member WHERE id=? LIMIT 1');
		$stm->execute(array($_id));
		return ($stm->rowCount()>0)?$stm->fetchObject(__CLASS__):new self();
	}
	public static function getList(PDO $db,$show=NULL,$query=''){
		$where=$show=="true"?'exp_date > NOW()':($show=="false"?'(exp_date <= NOW())':'1');
		if(strlen($query)>0){
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
			$param=array(':q'=>$query,':kw'=>'%'.$query.'%');
		}else{
			$param=array();
			$sql='SELECT * FROM member WHERE '.$where.' ORDER BY _id';
		}
		$stm=$db->prepare($sql);
		$stm->execute($param);
		return $stm->fetchAll(PDO::FETCH_CLASS,__CLASS__);
	}
	public static function delete(PDO $db,$del_list){
		$stm=$db->prepare('DELETE FROM member WHERE _id IN ('.implode(',',array_fill(0,count($del_list),'?')).')');
		$stm->execute($del_list);
		return $stm->rowCount();
	}
	public function add(PDO $db,$getID=false){
		$stm=$db->prepare('INSERT INTO `member` (`fname` , `lname` , `phone` , `address` , `reg_date` , `exp_date`) VALUES (:f, :l, :p, :a, :r, :e)');
		$stm->bindValue(':f',$this->fname);
		$stm->bindValue(':l',$this->lname);
		$stm->bindValue(':p',$this->phone);
		$stm->bindValue(':a',$this->address);
		$stm->bindValue(':r',$this->reg_date);
		$stm->bindValue(':e',$this->exp_date);
		$stm->execute();
		$this->_id=$db->lastInsertId();
		return ($getID)?$this->_id:$this;
	}
	public function update(PDO $db,$rowCount=false){
		$stm=$db->prepare('UPDATE `member` SET `fname`=:f,`lname`=:l,`phone`=:p, `address`=:a,`reg_date`=:r,`exp_date`=:e WHERE `_id`=:i');
		$stm->bindValue(':i',$this->_id);
		$stm->bindValue(':f',$this->fname);
		$stm->bindValue(':l',$this->lname);
		$stm->bindValue(':p',$this->phone);
		$stm->bindValue(':a',$this->address);
		$stm->bindValue(':r',$this->reg_date);
		$stm->bindValue(':e',$this->exp_date);
		$stm->execute();
		return ($rowCount)?$stm->rowCount():$this;
	}
	public function renew(PDO $db,&$fee){
		if(time()<strtotime($this->exp_date.' -'.$config->MEMBER_PRE_RENEW_M.' month')){
			$fee=0;
			return $this;
		}elseif(time()>strtotime($this->exp_date.' +'.$config->MEMBER_POST_RENEW_M.' month')){
			$fee=$config->MEMBER_REGISTER_FEE;
			$day=strtotime('+'.$config->MEMBER_EXP_YEAR.' year',time());
		}else{
			$fee=$config->MEMBER_RENEW_FEE;
			$day=strtotime($this->exp_date.' +'.$config->MEMBER_EXP_YEAR.' year');
		}
		$this->exp_date=date('Y-m-d H:i:s',$this->exp_date);
		$stm=$db->prepare('UPDATE `member` SET `exp_date`=:e WHERE id=:i');
		$stm->bindValue(':e',$this->exp_date);
		$stm->bindValue(':i',$this->_id);
		return $this;
	}
}
?>