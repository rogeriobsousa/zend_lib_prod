<?php
class Base_View_Helper_Cpf extends Zend_View_Helper_FormText
{
	
	public function cpf($name, $value = null, $attribs = null)
	{
		
		if (!is_array($attribs)) {
			$attribs = array();
		}
		
//		echo " <script>
//					//Mascará da jquery para os campos
//					//Adiciona uma função no metodo
//					jQuery(function($){
//						$(\"#{$name}\").mask(\"999.999.999-99\",{completed:function(){cpfValidade($(\"#{$name}\"));}});
//					});
//			   </script>";
		$mascara = "<script>
					$(function(){
						$(\"#{$name}\").keypress(function(){
							mascara(this,cpf)
						})
						$(\"#{$name}\").blur(function(){
							cpfValidade($(\"#{$name}\"))
						})
					})
				   </script>";
		echo $mascara;
		$attribs['maxlength']=14;
		
		return $this->formText($name, $value, $attribs);
	}
}
?>