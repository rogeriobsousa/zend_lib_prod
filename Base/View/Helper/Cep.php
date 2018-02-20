<?php
class Base_View_Helper_Cep extends Zend_View_Helper_FormText
{
	
	public function cep($name, $value = null, $attribs = null)
	{
		
		if (!is_array($attribs)) {
			$attribs = array();
		}
		$attribs['onKeyDown'] = 'mascara(this,cep)';
		$attribs['maxlength']=9;
		
		return $this->formText($name, $value, $attribs);
	}
}
?>