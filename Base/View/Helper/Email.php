<?php
class Base_View_Helper_Email extends Zend_View_Helper_FormText
{

	public function email($name, $value = null, $attribs = null)
	{

		if (!is_array($attribs)) {
			$attribs = array();
		}
		
		$mascara = "<script>
					$(function(){
						$(\"#{$name}\").blur(function(){
							emailValidade($(\"#{$name}\"))
						})
					})
				   </script>";
		echo $mascara;
		return $this->formText($name, $value, $attribs);
	}
}
?>