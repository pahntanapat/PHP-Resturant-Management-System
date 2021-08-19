<?php
class StatFood{
	public $fullName='',$abbr='',$amount=0,$eatHere=NULL,$cancel=false,$note,$ing=array();
	public function __constructor($json=''){
		if(strlen($json)>0){
			$json=json_decode($json,true);
			$this->fullName=$json['name'];
			$this->abbr=$json['abbr'];
			$this->amount=$json['dish'];
			$this->eatHere=$json['eatHere'];
			$this->note=$json['note'];
			$this->cancel=$json['cancel'];
			$this->ing=$json['ing'];
		}
	}
	public function addIngredient($nameWithAbbr,$price,$parentName){
		$this->ing[]=array(
			'name'=>$nameWithAbbr,
			'price'=>$price,
			'group'=>$parentName
		);
	}
	public function pricePerDish(){
		$price=0;
		foreach($this->ing as $ing)
			$price+=$ing['price'];
		return $price;
	}
	public function totalPrice(){
		return $this->pricePerDish()*$this->amount;
	}
	public function __toString(){
		return json_encode(array(
			'name'=>$this->fullName,
			'abbr'=>$this->abbr,
			'note'=>$this->note,
			'eatHere'=>(boolean) $this->eatHere,
			'cancel'=>(boolean)$this->cancel,
			'dish'=>intval($this->amount),
			'time'=>new MongoDate(),
			'ingrd'=>$this->ing
		));
	}
}
?>