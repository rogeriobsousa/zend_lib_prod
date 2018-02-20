<?php
class Base_Controller_Action_Helper_MdicHash extends Zend_Controller_Action_Helper_Abstract
{
	private $mdicHash;
	
	public function __construct() {
	}

	// converte para hexadecimal
	private function hexConvert($passwd) {
		$hexConv = bin2hex($passwd);
		return $hexConv;
	}
	
	// converte para md5
	private function md5Conv($passwd) {
		$md5Conv = md5($passwd);
		return $md5Conv;
	}
	
	// converte para sha1
	private function sha1Conv($passwd) {
		$sha1Passwd = sha1($passwd);
		return $sha1Passwd;
	}
	
	/* 
	 * Realiza uma combina��o com os algor�timos acima para criar um
	 * hash espec�fico para o MDIC 
	 */
	private function doHashing($passwd) {		
		return $this->sha1Conv($this->md5Conv($this->hexConvert($passwd)));
	}
	
	// Retorna o hash gerado
	public function getMDICHash($passwd) {
		return $this->doHashing($passwd);
	}
}
?>