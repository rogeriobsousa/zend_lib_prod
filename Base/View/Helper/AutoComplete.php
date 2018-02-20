<?php
class Base_View_Helper_AutoComplete extends Zend_View_Helper_FormText
{
	
	public function autoComplete($name, $value = null, $attribs = null)
	{
		if (!is_array($attribs)) {
			$attribs = array('class'=>'span-2');
		}
		
		//busca a model a ser usada;
		$model = $attribs['model'];
		$method = ($attribs['method']) ? $attribs['method'] : null; 
		$fieldValue = ($attribs['fieldValue']) ? $attribs['fieldValue'] : null; 
		$fieldText = ($attribs['fieldText']) ? $attribs['fieldText'] : null; 
		
		$baseUrl = $attribs['baseUrl']; 
		$modulo = ($attribs['modulo']) ? $attribs['modulo'] : APP_MODULO_DEFAULT;
		
		$stringRequest .= 'method='.$method;
		$stringRequest .= '&fieldValue='.$fieldValue;
		$stringRequest .= '&fieldText='.$fieldText;
		$stringRequest .= '&model='.$model;
		
		$script = "<script>";
		$script .= "
		$(document).ready(function(){
			$('#tx_$name').autocomplete('../$modulo/$model/auto-complete?$stringRequest', {
				width: 260,
				selectFirst: false
			});
			
	
			$('#tx_$name').result(function(event, data, formatted) {
				if (data){
					$('#$name').val(data[1]);
				}
			})
				
	
			$('#tx_$name').blur(function() {
					var paramVal = '&q='+$('#tx_$name').val();		
					$.ajax({
						type: 'get',
						url: '../$modulo/$model/get-codigo-auto-complete?$stringRequest&'+paramVal,
						dataType: 'html',
						success: function(resp){
							$('#$name').val(resp);
						}
					})
			})
		});";
		$script .= "</script>";
		
		echo $script;
		
		//criar um campo hidden e um campo para o texto
		$campoHidden = "<input type='text' name='$name' id='$name' value='$value' required='{$attribs['required']}' fieldRequiredBind='tx_$name'/>";
		
		$campoTexto = $this->formText('tx_'.$name, $value, $attribs);
		
				
		return $campoHidden.$campoTexto;
	}
}
?>