<?php
class Base_View_Helper_AnoCombo extends Zend_View_Helper_FormSelect
{
	private $anoInicio     = 2006;
	private $anoQuantidade = 1;
	
	public function anoCombo($name, $value = null, $attribs = null, $anoQuantidade = null, $anoInicio = null){
		
		if(!is_null($anoQuantidade)){
			$this->anoQuantidade = $anoQuantidade;
		}
		
		if(!is_null($anoInicio)){
			$this->anoInicio = $anoInicio; 
		}
		
		//Array Ano inicio
		$arrAno = self::createArray();
		
		if (!is_array($attribs)) {
			$attribs = array('select' => 'select');
		}
		
		return $this->formSelect($name, $value, $attribs,$arrAno);
	}
	
	protected  function createArray()
	{
		
		
		$data  = (int)date('Y');
		$data += $this->anoQuantidade;
		$i     = $this->anoInicio;
		$arrAno[0] = "Ano";
		
		for($i;$i <= $data ;$i++){
			$arrAno[$i] = $i;
		}
		
		return $arrAno;
	}
}
?>