<?php
require_once 'MPDF57/mpdf.php';
require_once 'config.inc.php';
class Printing{
	const 
		FOLDER='_slip',
		
		MARGIN_L_R=3.175,
		MARGIN_T_B=0.79375,
		MARGIN_H_F=0,
		
		SLIP_ORDER='order.html',
		SLIP_BILL='bill.html',
		
		ORDER_NOT_CONFIRM=0,
		ORDER_CONFIRM=1,
		ORDER_PRINT=2,
		ORDER_CANCEL=4;
	private $slip, $htm;
	public $cus_id, $order_id;
	public function __construct($slip){
		$this->slip=file_get_contents($slip);
	}
	public function setOrder($row){
		$in=array();
		$in['{id}']=$row['id'];
		$this->order_id=$row['id'];
		$this->cus_id=$row['cus_id'];
		$in['{cus}']=$row['table_no']==NULL?'คุณ '.$row['cus_name']:'โต๊ะ '.$row['table_no'];
		$in['{eatAt}']=$row['eat_here']?'ทานที่ร้าน (for here)':'ห่อกลับบ้าน (take home)';
		$in['{abbr}']=$row['abbr'];
		$in['{full}']=$row['full'];
		$in['{amount}']=$row['amount'];
		$in['{price}']=$row['price'];
		$in['{subtotal}']=number_format($row['price']*$row['amount'],2);
		$in['{outstand}']=number_format($row['outstand'],2);
		$in['{total}']=number_format($row['price']*$row['amount']+$row['outstand'],2);
		$in['{time}']=$row['time'];
		$in['{kit}']=$row['kit'];
		
		$this->htm=strtr($this->slip,$in);
	}
	public function getHTML(){
		return $this->htm;
	}
	public function toPDF($toFile=true){
		$filename=$_SERVER['DOCUMENT_ROOT'].'/'.self::FOLDER.'/'.$this->cus_id.'/';
		if(!is_dir($filename))
			if(!@mkdir($filename,0777,true))
				throw new Exception('Cannot create new directory:'.$filename);

		$filename.=($this->order_id==NULL?'bill':$this->order_id).'.pdf';
		$pdf=new mPDF(
			'th',array($GLOBALS['config']->SLIP_WIDTH,$GLOBALS['config']->SLIP_HEIGHT),NULL,'norasi',
			self::MARGIN_L_R,self::MARGIN_L_R,
			self::MARGIN_T_B,self::MARGIN_T_B,
			self::MARGIN_H_F,self::MARGIN_H_F);
		$pdf->SetAutoFont(AUTOFONT_ALL);
		$pdf->WriteHTML($this->htm);
		$pdf->Output($filename,($toFile?'F':'I'));
		return $toFile?$filename:NULL;
	}
	public static function printPDF($path,$printer){
		if(strtoupper(substr(PHP_OS,0,3))=='WIN')
			$cmd='"'.$_SERVER['DOCUMENT_ROOT'].'\\'.$GLOBALS['config']->FOXIT_PATH.'" -t "'.$path.'" "'.$printer.'"';
		else
			$cmd='lpr -P '.$printer.' "'.$path.'"';
		return shell_exec($cmd);
	}
	
	public static function printOrder($db,$lim=false,$id=NULL,$reprint=false){
		require_once 'config.inc.php';
		if($lim===false) $lim=$GLOBALS['config']->PRINT_MAX_LOOP;
		if($lim==1 && $id!=NULL) $sid='AND order_list.id=:id';
		else $sid='';
		$message='';
		$sql=<<<SQL
SELECT
	order_list.id AS id, cus_id, full_menu AS full,
	abbr_menu AS abbr, note, eat_here, price, amount, time,
	kitchen.name AS kit, printer
FROM order_list
INNER JOIN kitchen ON kitchen.id=order_list.kit_id
WHERE state=:st {$sid}
ORDER BY time ASC, id ASC
LIMIT :lim
SQL;
		$stm=$db->prepare($sql);
		$stm->bindValue(':st',self::ORDER_CONFIRM,PDO::PARAM_INT);
		$stm->bindValue(':lim',$lim,PDO::PARAM_INT);
		if($lim==1 && $id!=NULL) $stm->bindValue(':id',$id);
		$stm->execute();
		
		$sql=<<<SQL
SELECT 
	table_no, cus_name, COALESCE(SUM(order_list.price),0) AS outstand
FROM order_customer
LEFT JOIN order_list
ON order_list.cus_id=order_customer.id AND order_list.state=?
WHERE order_customer.id=?
GROUP BY order_customer.id
LIMIT 1
SQL;
		$order=new self(Printing::SLIP_ORDER);
		foreach($stm->fetchAll(PDO::FETCH_ASSOC) as $row){
			$stmt=$db->prepare($sql);
			$stmt->bindValue(1,self::ORDER_CONFIRM|self::ORDER_PRINT,PDO::PARAM_INT);
			$stmt->bindValue(2,$row['cus_id']);
			$stmt->execute();
			$row=array_merge($row,$stmt->fetch(PDO::FETCH_ASSOC));
			$order->setOrder($row);
			$file=$order->toPDF();
			if($row['printer']!=NULL)
				Printing::printPDF($file,$row['printer']);
				
			if(!$reprint||$row['printer']==NULL){
				$stmt=$db->prepare('UPDATE order_list SET state=? WHERE id=?');
				$stmt->bindValue(1,self::ORDER_PRINT|self::ORDER_CONFIRM,PDO::PARAM_INT);
				$stmt->bindValue(2,$row['id']);
				$stmt->execute();
			}
		}
		return str_replace($_SERVER['DOCUMENT_ROOT'],'',$file);
	}
}
?>