<?php
class Base_View_Helper_DataHora extends Zend_View_Helper_FormText
{
	
	public function DataHora($name, $value = null, $attribs = null)
	{
		
		if (!is_array($attribs)) {
			$attribs = array();
		}
		
		echo " <script>
					//Mascará da jquery para os campos
					//Adiciona uma função no metodo
					jQuery(function($){
						$(\"#{$name}\").mask(\"99/99/9999 99:99:99\",{completed:function(){dateTimeValidade($(\"#{$name}\"));}});
					});
			   </script>";
		
		return $this->formText($name, $value, $attribs);
	}
}
?>