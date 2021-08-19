<?php
class SKAjax{
	private $arrayAction;
	public $result,$message;
	const ALERT="alert", REDIRECT="redirect", EVALUTE="eval", FOCUS="focus",
		SET_TEXT="setText", SET_HTML="setHTML", SET_VAL="setVal",
		RELOAD_CAPTCHA="reloadCAPTCHA", SCROLL_TO="scrollTo",
		LOGIN_DIALOG="Need log in",PIN_DIALOG="Need PIN",
		RESET_FORM="resetForm", RECALL_SERVER="recall";
	
	public function __construct($json=NULL){
		$this->arrayAction=array();
		$this->result=false;
		$this->message='';
		if($json!=NULL)
			self::fromJSON($json);
	}
	public function __destruct(){
		unset($this->arrayAction);
	}
	public function getAction($id=NULL){
		if($id==NULL)
			return $this->arrayAction;
		elseif($id<count($this->arrayAction))
			return $this->arrayAction[$id];
		else return NULL;
	}
	public function setAction($id,$act,$how=NULL){
		$arr=array('act'=>$act);
		switch($act){
			case self::SET_HTML:
			case self::SET_TEXT:
			case self::SET_VAL:
				$arr['selector']=self::getOption($how,'selector');
			case self::ALERT:
				$arr['message']=self::getOption($how,'message');break;
			case self::REDIRECT:
				$arr['url']=self::getOption($how,'url');break;
			case self::EVALUTE:
				$arr['script']=self::getOption($how,'script');break;
			case self::RECALL_SERVER:
				$arr['call']=(boolean) self::getOption($how,'call');break;
			case self::RESET_FORM:
			case self::RELOAD_CAPTCHA: break;
			case self::SCROLL_TO:
			case self::FOCUS:
				$arr['selector']=self::getOption($how,'selector');break;
		}
		$this->arrayAction[$id]=$arr;
		return count($this->arrayAction);
	}
	
	public function addAction($act,$how=NULL){
		return $this->setAction(count($this->arrayAction),$act,$how);
	}
	
	public function addHtmlTextVal($action=jsonAjax::SET_HTML,$selector="body",$msg=""){
		return $this->addAction($action,array('selector'=>$selector,'message'=>$msg));
	}
	public function addShowDialog($dialog,$form='form'){
		return $this->addAction(self::EVALUTE,'$('.$form.').DialogAndSubmit('.$dialog.');');
	}
	public function alert($message){
		return $this->addAction(self::ALERT,$message);
	}
	
	public function removeAction($id=-1){
		if($id<0){
			$this->arrayAction=array();
			return 0;
		}
		unset($this->arrayAction[$id]);
		$this->arrayAction=array_values($this->arrayAction);
		return count($this->arrayAction);
	}
	
	public function toJSON($option=0,$depth=512){
		return json_encode(array(
			'result'=>$this->result,
			'message'=>$this->message,
			'action'=>$this->arrayAction
		),$option,$depth);
	}
	public function fromJSON($json,$assoc=false,$depth=512,$option=0){
		$r=json_decode($json,$assoc,$depth,$option);
		$this->result=$r['result'];
		$this->message=$r['message'];
		$this->arrayAction=$r['action'];
		unset($r);
	}
	public function __toString(){
		return $this->toJSON();
	}
	
	public static function getOption($arr,$key=0){
		if(is_array($arr)){
			if(isset($arr[$key])) return $arr[$key];
			else return $arr[0];
		}elseif(is_object($arr)){
			if(isset($arr->{$key})) return $arr->{$key};
			else return $arr->__toString();
		}else return $arr;
	}
}
?>