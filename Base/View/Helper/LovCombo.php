<?php
class Base_View_Helper_LovCombo extends Zend_View_Helper_FormText
{
	
	public function lovCombo($name, $value = null, $attribs = null, array $dados=null)
	{
//		$fieldText = $attribs['fieldText'];
//		$fieldValue = $attribs['fieldValue'];

		//$valores = implode(",",$value);
		$valores = $value;

		if (!is_array($attribs)) {
			$attribs = array('class'=>'span-2');
		}
		$div = "<div id='div_tx_$name' style='float:left'></div>";
		$arrDados = array();
		foreach($dados as $key=>$val){
			$explodeVal = explode("|",$val);
			if($explodeVal[1]=='S'){
				$img = " <img src=\"public/images_widestar/Alert_peq.png\"/> Em atendimento";
			}else{
				$img = null;
			}
			
			$arrDados[] = "['{$key}','{$explodeVal[0]}','']";
		}
		$jsonDados = implode(",",$arrDados);
		$script = "<script>";
		$script .= "Ext.BLANK_IMAGE_URL = '../public/img/s.gif';
					var tx_$name;
					Ext.onReady(function() {
						tx_$name = new Ext.ux.form.LovCombo({
							id:'tx_$name'
							,renderTo:'div_tx_$name'
							,width:200
							,hideOnSelect:false
							,maxHeight:200
							,store:[
								$jsonDados
							]
							,readOnly: true
							,listeners:{
								blur:function() {
									$('#$name').val(this.getValue());
								}
							}
							,triggerAction:'all'		
						});
						$('#$name').val('$valores');
						tx_$name.setValue('$valores');
						$('#tx_$name').addClass('{$attribs['class']}');

					})
				";
		$script .= "</script>";
		//criar um campo hidden e um campo para o texto
		$campoHidden = "<input type='hidden' size='10' class='' name='$name' id='$name' value='$value' fieldRequiredBind='tx_$name'/>";
		
		//$campoTexto = $this->formText('tx_'.$name, $value, $attribs);
		return $campoHidden.$div.$script;
	}
}
?>