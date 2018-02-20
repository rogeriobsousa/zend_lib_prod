<?php
class Base_View_Helper_NumContrato extends Zend_View_Helper_FormText
{
	
	public function numContrato($name, $value = null, $attribs = null)
	{
		if (!is_array($attribs)) {
			$attribs = array('class'=>'span-2');
		}
		
		echo " <script>
					//Mascará da jquery para os campos
					//Adiciona uma função no metodo
					jQuery(function($){
						$(\"#{$name}\").mask(\"99/9999\",{});
					});
			   </script>";
			$botaoNumContrato  = $this->formText($name, $value, $attribs);
			return $botaoNumContrato; 
	}
}
?>