<?php
class Base_View_Helper_DataString extends Zend_View_Helper_FormText
{
	
public function dataString($name, $value = null, $attribs = null)
	{
		if (!is_array($attribs)) {
			$attribs = array();
		}
//		echo " <script>
//					//Mascará da jquery para os campos
//					//Adiciona uma função no metodo
//					jQuery(function($){
//						$(\"#{$name}\").mask(\"99/99/9999\",{completed:function(){dateValidade($(\"#{$name}\"));}});
//					});
//			   </script>";
		
		$mascara = "<script>
					$(function(){
						$(\"#{$name}\").keypress(function(){
							mascara(this,data)
						})
						$(\"#{$name}\").blur(function(){
							dateValidade($(\"#{$name}\"))
						})
					})
				   </script>";
		echo $mascara;
		$attribs['size']=10;
		$attribs['maxlength']=10;
		$attribs['tipo']='data';
			   
		$botaoData  = $this->formText($name, $value, $attribs);
		//$botaoData .= "<input class='button' type='button' style='border-left: 1px solid rgb(204, 204, 204); height: 20px; width: 21px; background-image: url(public/img/del.png); background-position: -2px 0px; background-repeat: no-repeat;' />";

			return $botaoData; 
	}	
}
?>