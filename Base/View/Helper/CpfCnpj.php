<?php
class Base_View_Helper_CpfCnpj extends Zend_View_Helper_FormText
{
	
	public function cpfcnpj($name, $value = null, $attribs = null)
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
		
		$condicionador = $attribs['condicionador'];
		
		$mascara = "<script>
					$(function(){
					
						$(\"#{$name}\").keypress(function(){
							if($('#{$condicionador}').val()=='F'){
								$('#{$name}').attr('maxlength','14')
								$('#{$name}').attr('minlength','14')
								Mascara(this,Cpf)
							}else{
								$('#{$name}').attr('maxlength','18')
								$('#{$name}').attr('minlength','18')
								Mascara(this,Cnpj)
							}
								
						})
						$(\"#{$name}\").blur(function(){
							if($('#{$condicionador}').val()=='F'){
								cpfValidade($(\"#{$name}\"))
							else
								cnpjValidade($(\"#{$name}\"))
								
						})
					})
				   </script>";
		echo $mascara;
		
		return $this->formText($name, $value, $attribs);
	}
}
?>