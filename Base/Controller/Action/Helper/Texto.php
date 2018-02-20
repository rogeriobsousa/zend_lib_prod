<?php
class Base_Controller_Action_Helper_Texto extends Zend_Controller_Action_Helper_Abstract
{
	public function upper($value)
	{
		return strtoupper($value);
	}
	
	public function lower($value)
	{
		return strtolower($value);
	}
        
        public function add_mask_telefone($mask,$str)
	{
            $str = str_replace(" ","",$str);
            if (strlen($str) == 10) {
                for($i=0;$i<strlen($str);$i++){
                    $mask[strpos($mask,"#")] = $str[$i];
                }
            } else {
                $mask = $str;
            }
            return $mask;
	}
}