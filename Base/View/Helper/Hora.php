<?php
class Base_View_Helper_Hora extends Zend_View_Helper_FormText
{
	
	public function hora($name, $value = null, $attribs = null)
	{
		if (!is_array($attribs)) {
			$attribs = array('class'=>'span-2');
		}
		
		echo " <script>
					//Mascará da jquery para os campos
					//Adiciona uma função no metodo
					jQuery(function($){
						$(\"#{$name}\").mask(\"99:99\",{
							completed:function(){
								valida_hora_minuto($(\"#{$name}\"));
							}
						});
					});
			   </script>";
			$botaoHora  = $this->formText($name, $value, $attribs);
			return $botaoHora; 
	}
}
?>