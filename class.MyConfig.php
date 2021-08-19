<?php
class MyConfig extends stdClass{
	public static $SAVE_CONFIG='config.save.php';
	public function __get($n){
		if(defined("static::$n")) return constant("static::$n");
		else throw new Exception('No property named '.$n.' in config');
	}
	public function save(){
		return file_put_contents(static::$SAVE_CONFIG, '<?php return '.var_export($this,true).' ; ?>');
	}
	public function reset(){
		return file_put_contents(static::$SAVE_CONFIG, '<?php return new '.get_called_class().'(); ?>');
	}
	public static function __set_state($prop){
		$obj=new static();
		foreach($prop as $k=>$v)
			$obj->$k=$v;
		return $obj;
	}
	public static function load(){
		try{
			return require static::$SAVE_CONFIG;
		}catch(Exception $e){
			$obj=new static();
			$obj->reset();
			return static::load();
		}
	}
}
?>