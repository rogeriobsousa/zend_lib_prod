<?php
class Zend_View_Helper_FormSelectAutoComplete
{
	public function formSelectAutoComplete( $name, $value, $data, $columnId=null, $columnDesc=null, $extra=null )
	{
		$strOptions = '';
		$selected = '';
		if( get_class( $data ) == 'Zend_Db_Table_Rowset' ){
		    
		    
			foreach( $data as $arDados ){
				if( $value == $arDados->$columnId ){
					$selected = 'selected';
				}
				$strOptions .= '<option value="'. $arDados->$columnId .'" '. $selected .'>'. $arDados->$columnDesc .'</option>';
				$selected = '';
			}
		}else{
		    
			
		    include_once "tables/$data.php";
			$table = new $data();
			$result = $table->fetchAll();
			$columnId = $columnId ? $columnId : 'CD_' . strtoupper($data);
			$columnDesc = $columnDesc ? $columnDesc : 'TX_' . strtoupper($data);
			foreach( $result as $arDados ){
				if( $value == $arDados->$columnId ){
					$selected = 'selected';
				}
				$strOptions .= '<option value="'. $arDados->$columnId .'" '. $selected .'>'. $arDados->$columnDesc .'</option>';
				$selected = '';
			}
		}
		
		
		$htmlSelect  = '<select ' . $extra . ' id="combo_'. $name .'" name="'. $name .'">';
		$htmlSelect .= $strOptions;
		$htmlSelect .= '</select>';
		
		$scrpit  = '<script>';
		$scrpit .= 'var var_'. $name .'=dhtmlXComboFromSelect("combo_'. $name .'");';
	  	$scrpit .= 'var_'. $name .'.enableFilteringMode(true);';
		$scrpit .= '</script>';
		
		return $htmlSelect . $scrpit;
	}
}