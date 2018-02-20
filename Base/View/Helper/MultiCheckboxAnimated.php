<?php
class Base_View_Helper_MultiCheckBoxAnimated extends Base_Form_Element_MultiCheckbox
{
	
	public function multiCheckBoxAnimated($name, $value = null, $attribs = null)
	{
		if (!is_array($attribs)) {
			$attribs = array();
		}
		
		echo " <script>
					//Mascará da jquery para os campos
					//Adiciona uma função no metodo
					jQuery(function($){
						$(\"#{$name}\").mask(\"(99) 9999-9999\");
					});
			   </script>";
		
		return $this->formText($name, $value, $attribs);
	}
}
?>