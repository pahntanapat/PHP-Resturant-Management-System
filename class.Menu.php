<?php
abstract class Menu{
	const
		FOOD_ING="menu_food_ing",
		FOOD_FIX="menu_food_fix",
		FOOD_ADJ="menu_food_adj",
		
		DIR_FOOD="menu_dir_food",
		DIR_ING="menu_dir_ing",
		
		ROOT_FOOD="food",
		ROOT_ING="ing";
	
	public $db, $id, $tb, $name='', $prn; //prn = parent
	abstract public function del();
	
	public function __construct(PDO $db, $id,$name,$prn){
		$this->id=$id;
		$this->name=$name;
		$this->prn=$prn;
		$this->db=$db;
	}
	public static function type($tb){
		switch($tb){
			case self::FOOD_ING: return "ส่วนประกอบอาหาร (เช่น เส้น, ขนาด)";
			case self::FOOD_FIX: return "อาหารที่กำหนดไว้แล้ว (เช่น อาหารจานเดียว)";
			case self::FOOD_ADJ: return "อาหารที่เลือกส่วนประกอบได้ (เช่น ก๋วยเตี๋ยว - ขนาด เส้น .., กาแฟ - ขนาด)";
			case self::DIR_FOOD: return "หมวดหมู่อาหาร";
			case self::DIR_ING: return "หมวดหมู่ส่วนประกอบอาหาร";
			default: return '';
		}
	}
	public static function set($val,$opt,$check=false){
		if($val===$check) return $opt;
		else return $val;
	}
	public static function check(){
		$i=func_num_args();
		if($i<=1) return true;
		$args=func_get_args();
		$compare=$args[0];
		foreach($args as $v)
			if($v===$compare) $i--;
		return ($i===0);
	}
}
abstract class Dir extends Menu{
	public function del(){
		if($this->tb==self::DIR_FOOD){
			$this->db->prepare('DELETE FROM '.(self::FOOD_ADJ).' WHERE parent=?')->execute(array($this->id));
			$this->db->prepare('DELETE FROM '.(self::FOOD_FIX).' WHERE parent=?')->execute(array($this->id));
		}else{
			$this->db->prepare('DELETE FROM '.(self::FOOD_ING).' WHERE parent=?')->execute(array($this->id));
		}
		$ch=$this->getChildren();
		foreach($ch as $v)	$v->del();
		return $this->db->prepare('DELETE FROM '.($this->tb).' WHERE id=?')->execute(array($this->id));	
	}
}
class FoodDir extends Dir{
	public static function get($id,PDO $db, $dummy=false){
		if($dummy) return new FoodDir($db,$id,'',0);
		$stm=$db->prepare('SELECT name, parent FROM '.self::DIR_FOOD.' WHERE id=?');
		$stm->execute(array($id));
		$row=$stm->fetch(PDO::FETCH_NUM);
		return new self($db, $id, $row[0],$row[1]);
	}
	public function __construct(PDO $db, $id,$name,$prn){
		parent::__construct($db, $id,$name,$prn);
		$this->tb=self::DIR_FOOD;
	}
	public function getParent(){
		return self::get($this->prn,$this->db);
	}
	public function getChildren(){
		$stm=$this->db->prepare("SELECT id, name FROM {$this->tb} WHERE parent=? ORDER BY name");
		$stm->execute(array($this->id));
		$re=array();
		while($row=$stm->fetch(PDO::FETCH_NUM))
			$re[]=new self($this->db,$row[0],$row[1],$this->id);
		return $re;
	}
	public function getFix(){
		$stm=$this->db->prepare("SELECT id, name, abbr, detail, price, kit_id, state FROM ".self::FOOD_FIX." WHERE parent=?");
		$stm->execute(array($this->id));
		$arr=array();
		while($row=$stm->fetch(PDO::FETCH_OBJ))
			$arr[]=new FixFood($this->db,$row->id,$row->name,$this->id,$row->detail,$row->abbr,$row->price,$row->kit_id,$row->state);
		return $arr;
	}
	public function getAdj(){
		$stm=$this->db->prepare("SELECT id, name, abbr, detail, price, kit_id, state, ingredient AS ing FROM ".self::FOOD_ADJ." WHERE parent=?");
		$stm->execute(array($this->id));
		$arr=array();
		while($row=$stm->fetch(PDO::FETCH_OBJ))
			$arr[]=new AdjFood($this->db,$row->id,$row->name,$this->id,$row->detail,$row->abbr,$row->price,$row->kit_id,$row->state,$row->ing);
		return $arr;
	}
	public function add($name,$jumpToIt=false){
		$stm=$this->db->prepare("INSERT INTO {$this->tb} (name, parent) VALUES (?,?)");
		$stm->execute(array($name,$this->id));
		return $jumpToIt?new self($this->db,$this->db->lastInsertId(),$name,$this->id):$this;
	}
	public function update($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET name=?, parent=? WHERE id=?");
		$stm->execute(array(
			$this->name,
			$this->prn,
			$this->id
		));
		return ($row_count)?$stm->rowCount():$this;
	}
}
class IngDir extends Dir{
	public $lim=0;
	public static function get($id, PDO $db, $dummy=false){
		if($dummy) return new IngDir($db,$id,'',0,0);
		$stm=$db->prepare('SELECT name, parent, lim FROM '.self::DIR_ING.' WHERE id=?');
		$stm->execute(array($id));
		$row=$stm->fetch(PDO::FETCH_NUM);
		return new self($db,$id,$row[0],$row[1],$row[2]);
	}
	public function __construct(PDO $db,$id,$name,$prn,$lim){
		parent::__construct($db,$id,$name,$prn);
		$this->tb=self::DIR_ING;
		$this->lim=$lim;
	}
	public function getParent(){
		return self::get($this->prn,$this->db);
	}
	public function getChildren(){
		$stm=$this->db->prepare("SELECT id, name, lim FROM {$this->tb} WHERE parent=? ORDER BY name");
		$stm->execute(array($this->id));
		$re=array();
		while($row=$stm->fetch(PDO::FETCH_NUM))
			$re[]=new self($this->db,$row[0],$row[1],$this->id,$row[2]);
		return $re;
	}
	public function getIng(){
		$stm=$this->db->prepare("SELECT id, name, abbr, detail, price, state FROM ".self::FOOD_ING." WHERE parent=?");
		$stm->execute(array($this->id));
		$arr=array();
		while($row=$stm->fetch(PDO::FETCH_OBJ))
			$arr[]=new IngFood($this->db,$row->id,$row->name,$this->id,$row->detail,$row->abbr,$row->price,$row->state);
		return $arr;
	}
	public function add($name,$lim=0,$jumpToIt=false){
		$stm=$this->db->prepare("INSERT INTO {$this->tb} (name, parent, lim) VALUES (?,?,?)");
		$stm->execute(array($name,$this->id,$lim));
		return $jumpToIt?new self($this->db,$this->db->lastInsertId(),$name,$this->id,$lim):$this;
	}
	public function update($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET name=?, parent=?, lim=? WHERE id=?");
		$stm->execute(array($this->name, $this->prn, $this->lim, $this->id));
		return ($row_count)?$stm->rowCount():$this;
	}
}
abstract class Food extends Menu{
	const 
		ST_ONLY_SB=1,
		ST_FREE_ONLY=2,
		ST_TAKE_HOME=4,
		ST_FOR_HERE=8;
	public $state,$detail,$price,$abbr;
	public function __construct($tb, PDO $db,$id,$name,$prn,$detail,$abbr,$price,$state){
		parent::__construct($db,$id,$name,$prn);
		$this->tb=$tb;
		$this->detail=$detail;
		$this->price=$price;
		$this->abbr=$abbr;
		$this->state=$state;
	}
	public function setState($what,$state){
		switch($what){
			case self::ST_FOR_HERE:
			case self::ST_FREE_ONLY:
			case self::ST_ONLY_SB:
			case self::ST_TAKE_HOME:
				if($state) $this->state|=$what;
				else $this->state&=~$what;
		}
		return $this;
	}
	public function isState($what){
		return ($this->state&$what)!=0;
	}
	public function updateState($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET state=:s WHERE id=:id");
		$stm->bindValue(':s',$this->state, PDO::PARAM_INT);
		$stm->bindValue(':id',$this->id);
		$stm->execute();
		return $row_count?$stm->rowCount():$this;
	}
	public function getParent(){
		return FoodDir::get($this->prn,$this->db);
	}
	public function del(){
		return $this->db->prepare("DELETE FROM {$this->tb} WHERE id=?")->execute(array($this->id));
	}
}
class Kitchen{
	public $id, $db;
	const KIT="kitchen";
	public function __construct(PDO $db,$kit_id){
		$this->db=$db;
		$this->id=$kit_id;
	}
	public function kitName(){
		$stm=$this->db->prepare("SELECT name FROM ".self::KIT." WHERE id=? LIMIT 1");
		$stm->execute(array($this->id));
		return ($stm->rowCount()>0)?$stm->fetchColumn():'';
	}
}
class FixFood extends Food{
	public $kitchen;
	public static function get($id, PDO $db, $dummy=false){
		if($dummy) return new self($db,$id,'',0,'','',0,0,0);
		$stm=$db->prepare("SELECT name, abbr, detail, parent AS prn, price, kit_id, state FROM ".self::FOOD_FIX." WHERE id=?");
		$stm->execute(array($id));
		$row=$stm->fetch(PDO::FETCH_OBJ);
		return new self($db,$id,$row->name,$row->prn,$row->detail,$row->abbr,$row->price,$row->kit_id,$row->state);
	}
	public function __construct(PDO $db,$id,$name,$prn,$detail,$abbr,$price,$kit_id,$state){
		parent::__construct(self::FOOD_FIX,$db,$id,$name,$prn,$detail,$abbr,$price,$state);
		$this->kitchen=new Kitchen($db,$kit_id);
	}
	public function update($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET parent=:pr, name=:n, detail=:d, price=:p, state=:s, abbr=:a, kit_id=:k WHERE id=:id");
		$stm->bindValue(':pr',$this->prn);
		$stm->bindValue(':n',$this->name);
		$stm->bindValue(':d',$this->detail);
		$stm->bindValue(':p',$this->price);
		$stm->bindValue(':s',$this->state, PDO::PARAM_INT);
		$stm->bindValue(':a',$this->abbr);
		$stm->bindValue(':id',$this->id);
		$stm->bindValue(':k',$this->kitchen->id);
		$stm->execute();
		return $row_count?$stm->rowCount():$this;
	}
	public static function add(PDO $db,$name,$parent,$detail='',$abbr='',$price=0,$kit_id=0,$state=0,$jumpToIt=false){
		$tmp=self::get(0,$db,true);
		$tmp=$tmp->create($name,$parent,$detail,$abbr,$price,$kit_id,$state,true);
		return ($jumpToIt)?$tmp:$tmp->id;
	}
	public function create($name,$parent,$detail='',$abbr='',$price=0,$kit_id,$state=0,$jumpToIt=false){
		$stm=$this->db->prepare("INSERT INTO {$this->tb} (name, abbr, parent, detail, price, kit_id, state) VALUES (:n,:a,:pr,:d,:p,:k,:s)");
		$stm->bindValue(':n',$name);
		$stm->bindValue(':a',$abbr);
		$stm->bindValue(':pr',$parent);
		$stm->bindValue(':d',$detail);
		$stm->bindValue(':p',$price);
		$stm->bindValue(':s',$state, PDO::PARAM_INT);
		$stm->bindValue(':k',$kit_id);
		$stm->execute();
		return ($jumpToIt)?new self($this->db,$this->db->lastInsertId(),$name,$this->prn,$detail,$abbr,$price,$state):$this;
	}
}
class IngFood extends Food{
	public static function get($id, PDO $db, $dummy=false){
		if($dummy) return new self($db,$id,'',0,'','',0,0);
		$stm=$db->prepare("SELECT name, abbr, detail, parent AS prn, price, state FROM ".self::FOOD_ING." WHERE id=?");
		$stm->execute(array($id));
		$row=$stm->fetch(PDO::FETCH_OBJ);
		return new self($db,$id,$row->name,$row->prn,$row->detail,$row->abbr,$row->price,$row->state);
	}
	public function __construct(PDO $db,$id,$name,$prn,$detail,$abbr,$price,$state){
		parent::__construct(self::FOOD_ING,$db,$id,$name,$prn,$detail,$abbr,$price,$state);
	}
	public function getParent(){
		return IngDir::get($this->prn,$this->db);
	}
	public static function add(PDO $db,$name,$parent,$detail='',$abbr='',$price=0,$state=0,$jumpToIt=false){
		$tmp=self::get(0,$db,true);
		$tmp=$tmp->create($name,$parent,$detail,$abbr,$price,$state,true);
		return ($jumpToIt)?$tmp:$tmp->id;
	}
	public function update($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET parent=:pr, name=:n, detail=:d, price=:p, state=:s, abbr=:a WHERE id=:id");
		$stm->bindValue(':pr',$this->prn);
		$stm->bindValue(':n',$this->name);
		$stm->bindValue(':d',$this->detail);
		$stm->bindValue(':p',$this->price);
		$stm->bindValue(':s',$this->state, PDO::PARAM_INT);
		$stm->bindValue(':a',$this->abbr);
		$stm->bindValue(':id',$this->id);
		$stm->execute();
		return $row_count?$stm->rowCount():$this;
	}
	public function create($name,$parent,$detail='',$abbr='',$price=0,$state=0,$jumpToIt=false){
		$stm=$this->db->prepare("INSERT INTO {$this->tb} (name, abbr, parent, detail, price, state) VALUES (:n,:a,:pr,:d,:p,:s)");
		$stm->bindValue(':n',$name);
		$stm->bindValue(':a',$abbr);
		$stm->bindValue(':pr',$parent);
		$stm->bindValue(':d',$detail);
		$stm->bindValue(':p',$price);
		$stm->bindValue(':s',$state, PDO::PARAM_INT);
		$stm->execute();
		return ($jumpToIt)?new self($this->db,$this->db->lastInsertId(),$name,$this->prn,$detail,$abbr,$price,$state):$this;
	}
}
class AdjFood extends Food{
	public $ing='',$kitchen;
	public static function get($id,PDO $db,$dummy=false){
		if($dummy) return new self($db,$id,'','','','','',0,0,'');
		$stm=$db->prepare("SELECT name, abbr, detail, parent AS prn, price, kit_id, state, ingredient AS ing FROM ".self::FOOD_ADJ." WHERE id=?");
		$stm->execute(array($id));
		$row=$stm->fetch(PDO::FETCH_OBJ);
		return new self($db,$id,$row->name,$row->prn,$row->detail,$row->abbr,$row->price,$row->kit_id,$row->state,$row->ing);
	}
	public function __construct(PDO $db,$id,$name,$prn,$detail,$abbr,$price,$kitchen,$state,$ing=''){
		parent::__construct(self::FOOD_ADJ,$db,$id,$name,$prn,$detail,$abbr,$price,$state);
		$this->ing=$ing;
		$this->kitchen=new Kitchen($db,$kitchen);
	}
	public static function add(PDO $db,$name,$parent,$detail='',$abbr='',$price=0,$kit_id=0,$state=0,$ing='',$jumpToIt=false){
		$stm=$db->prepare("INSERT INTO ".self::FOOD_ADJ." (name, abbr, parent, detail, price, kit_id, state, ingredient) VALUES (:n,:a,:pr,:d,:p,:k,:s,:i)");
		$stm->bindValue(':n',$name);
		$stm->bindValue(':a',$abbr);
		$stm->bindValue(':pr',$parent);
		$stm->bindValue(':d',$detail);
		$stm->bindValue(':p',$price);
		$stm->bindValue(':s',$state);
		$stm->bindValue(':i',$ing);
		$stm->bindValue(':k',$kit_id);
		$stm->execute();
		return ($jumpToIt)?new self($db,$db->lastInsertId(),$name,$parent,$detail,$abbr,$price,$state):$db->lastInsertId();
	}
	public function create($name,$detail='',$abbr='',$price=0,$kit_id=0,$state=0,$ing='',$jumpToIt=false){
		$r=self::add($this->db,$name,$this->prn,$detail,$abbr,$price,$kit_id,$state,$ing,$jumpToIt);
		return ($jumpToIt)?$r:$this;
	}
	public function update($row_count=false){
		$stm=$this->db->prepare("UPDATE {$this->tb} SET parent=:pr, name=:n, detail=:d, price=:p, kit_id=:k, state=:s, abbr=:a, ingredient=:i WHERE id=:id");
		$stm->bindValue(':pr',$this->prn);
		$stm->bindValue(':n',$this->name);
		$stm->bindValue(':d',$this->detail);
		$stm->bindValue(':p',$this->price);
		$stm->bindValue(':s',$this->state, PDO::PARAM_INT);
		$stm->bindValue(':a',$this->abbr);
		$stm->bindValue(':i',$this->ing);
		$stm->bindValue(':id',$this->id);
		$stm->bindValue(':k',$this->kitchen->id);
		$stm->execute();
		return $row_count?$stm->rowCount():$this;
	}
	public function getIngID(){
		return explode("\n",$this->ing);
	}
	public function getIngObj($withID=false){
		$temp=array();
		$tmp=$this->getIngID();
		$stm=$this->db->prepare("SELECT * FROM ".self::DIR_ING." WHERE id IN (".implode(',',array_fill(0,count($tmp),'?')).") ORDER BY FIND_IN_SET(id,?)");
		$tmp[]=implode(',',$tmp);
		$stm->execute($tmp);
		while($row=$stm->fetch(PDO::FETCH_ASSOC)){
			$tmp=new IngDir($this->db,$row['id'],$row['name'],$row['parent'],$row['lim']);
			if($withID)		$temp[$row['id']]=$tmp;
			else		$temp[]=$tmp;
		}
		return $temp;
	}
	public function setIngID($arr){
		$this->ing=implode("\n",$arr);
		return $this;
	}
	public function setIngObj(IngDir $objArr){
		$arr=array();
		foreach($objArr as $v){
			$arr[]=$v->id;
		}
		return $this->setIngID($arr);
	}
}
function get($id,$db,$root=false,$dummy=false){
	if($root===false) $root=$_REQUEST['root'];
	switch($root){
		case Dir::ROOT_FOOD:
			return FoodDir::get($id,$db,$dummy);
		case Dir::ROOT_ING:
			return IngDir::get($id,$db,$dummy);
		default:		return NULL;
	}
}
function abbr($menu,$withName=false){
	return strlen($menu->abbr)>0?(($withName)?$menu->name.' ('.$menu->abbr.')':$menu->abbr):$menu->name;
}
?>