<?php
class Promotion{
	const
		IS_ACTIVE=1,
		DISCOUNT_PERCENT=2,
		ONLY_MEMBER=4;
	public $id, $name, $detail, $discount, $state;
	public static function getList(PDO $db, $toObj=true){
		$stm=$db->query('SELECT * FROM promotion');
		if($toObj) return $stm->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,__CLASS__);
		else return $stm;
	}
	public static function add(PDO $db,$name='',$detail='',$discount=0,$state=0,$getObj=false){
		$stm=$db->prepare('INSERT INTO promotion (name,detail,discount,state) VALUES (:n,:d,:dc,:s)');
		$stm->bindValue(':n',$name);
		$stm->bindValue(':d',$detail);
		$stm->bindValue(':dc',$discount);
		$stm->bindValue(':s',$state,PDO::PARAM_INT);
		$stm->execute();
		return ($getObj)?new self($db->lastInsertId(),$name,$detail,$discount,$state):$db->lastInsertId();
	}
	public static function load(PDO $db, $id){
		if($id<=0) return new self();
		$stm=$db->prepare('SELECT * FROM promotion WHERE id=? LIMIT 1');
		$stm->execute(array($id));
		$stm->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,__CLASS__);
		if($stm->rowCount()>0) return $stm->fetch();
		else return new self();
	}
	public function __construct($id=0,$name='',$detail='',$discount=0,$state=0){
		$this->id=$id;
		$this->name=$name;
		$this->detail=$detail;
		$this->discount=$discount;
		$this->state=$state;
	}
	public function is($state){
		return ($state&$this->state)!=0;
	}
	public function set($state,$bool=true){
		if($bool)
			$this->state|=$state;
		else
			$this->state&=~$state;
		return $this;
	}
	public function update(PDO $db,$rowCount=false){
		$stm=$db->prepare('UPDATE promotion SET name=:n, detail=:d ,discount=:dc, state=:s WHERE id=:i');
		$stm->bindValue(':n',$this->name);
		$stm->bindValue(':d',$this->detail);
		$stm->bindValue(':dc',$this->discount);
		$stm->bindValue(':s',$this->state,PDO::PARAM_INT);
		$stm->bindValue(':i',$this->id);
		$stm->execute();
		return ($rowCount)?$stm->rowCount():$this;
	}
	public function delete(PDO $db){
		$stm=$db->prepare('DELETE FROM promotion WHERE id=?');
		$stm->execute(array($this->id));
		return $stm->rowCount();
	}
	public function __toString(){
		return sprintf($this->is(self::DISCOUNT_PERCENT)?'%01.2f%%':'฿%01.2f',$this->discount);
	}
}
?>