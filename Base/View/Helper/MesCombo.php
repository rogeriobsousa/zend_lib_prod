<?php
class Base_View_Helper_MesCombo extends Zend_View_Helper_FormSelect
{
	public function mesCombo($name, $value = null, $attribs = null)
	{
		
		if (!is_array($attribs)) {
			$attribs = array('select' => 'select');
		}
		//Monta um array para os mêses do projeto 
		$arrMeses     = array();
		$arrMeses[0]  = 'Mês';
		$arrMeses[1]  = 'Janeiro';
		$arrMeses[2]  = 'Fevereiro';
		$arrMeses[3]  = 'Março';
		$arrMeses[4]  = 'Abril';
		$arrMeses[5]  = 'Maio';
		$arrMeses[6]  = 'Junho';
		$arrMeses[7]  = 'Julho';
		$arrMeses[8]  = 'Agosto';
		$arrMeses[9]  = 'Setembro';
		$arrMeses[10] = 'Outubro';
		$arrMeses[11] = 'Novembro';
		$arrMeses[12] = 'Dezembro';

		return $this->formSelect($name, $value, $attribs,$arrMeses);
	}
}
?>