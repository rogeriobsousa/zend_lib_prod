<?php
class Base_Controller_Action_Helper_Valores extends Zend_Controller_Action_Helper_Abstract
{
	/*
	 * Entrada no Banco de Dados
	 * Saida do Banco de Dados
	 */
	public function converter($value, $destino)
	{
		if($value){
			switch ($destino) {
				case 'banco':
					$value = str_replace('.','', $value);
					$value = str_replace(',','.', $value);
				break;
				case 'tela':
					$value = str_replace('.',',', $value);
	//				$value = number_format($value, 2, ',', '.');
				break;
				break;
			}
		}else{
			$value = null;
		}
		return trim($value);
	}
	
	
	/**
	 * Método para subtracao de datas retornando em dias 
	 * 
	 * @author Rogerio Baptista
	 * @since 19/11/2008
	 * @param text $fromDate  data inicio
	 * @param text $$to     data fim
	 * @return $dia 
	 */
	public static function date_diff($fromDate, $to) {
	list($from_year, $from_month, $from_day) = explode("-", $fromDate);
	list($to_year, $to_month, $to_day) = explode("-", $to);
	$from_date = mktime(0,0,0,$from_month,$from_day,$from_year);
	$to_date = mktime(0,0,0,$to_month,$to_day,$to_year);
	$days = ($to_date - $from_date)/86400;
	/*Adicionado o ceil($days) para garantir que o resultado seja sempre um n�mero inteiro */
	return ceil($days);
	}
	
	public function getSegundos($hora){
		list($horas,$minutos,$segundos) = explode(":", $hora);
	
		$horas = $horas * 3600;
		$minutos = $minutos * 60;
		
		$total = $horas + $minutos + $segundos;
		return $total;
	}
	
	public function converteParaHora($total){
		$time = ($total/3600);
		list($horas) = explode(".", $time);
		$resto_segundos = ($total % 3600);// resto da divisao por 3600
		$c = ($resto_segundos/60);
		list($minutos) = explode(".", $c);
		$segundos = ($total % 60);
		return str_pad($horas,2,'0',STR_PAD_LEFT).":".str_pad($minutos,2,'0',STR_PAD_LEFT).":".str_pad($segundos,2,'0',STR_PAD_LEFT);
	}
	
	public function hour_diff($horaAnterior, $horaPosterior){
		list($horas_ant,$minutos_ant,$segundos_ant) = explode(":", $horaAnterior);
		list($horas_pos,$minutos_pos,$segundos_pos) = explode(":", $horaPosterior);
	
		$horas_ant = $horas_ant * 3600;
		$minutos_ant = $minutos_ant * 60;
		
		$horas_pos = $horas_pos * 3600;
		$minutos_pos = $minutos_pos * 60;
		
		$total_ant = $horas_ant + $minutos_ant + $segundos_ant;
		$total_pos = $horas_pos + $minutos_pos + $segundos_pos;
		
		$total = ($total_pos - $total_ant);
		return $total;
	}
	
	public function verificaMask($valor){
		
		$d1 = substr($valor,0,1);
		$d2 = substr($valor,1,1);
		$d3 = substr($valor,2,1);
		$d4 = substr($valor,3,1);
		$d5 = substr($valor,4,1);
		$d6 = substr($valor,5,1);
		$d7 = substr($valor,6,1);
		$d8 = substr($valor,7,1);
		$d9 = substr($valor,8,1);
		$d10 = substr($valor,9,1);
		
		if(is_numeric($d1) && is_numeric($d2) && ($d3=='/') 
			&& is_numeric($d4) && is_numeric($d5) && ($d6=='/') 
			&& is_numeric($d7) && is_numeric($d8) && is_numeric($d9) && is_numeric($d10)){
				$valor = $this->converter($valor,'DD/MM/YYYY','YYYY-MM-DD');
				return $valor;
				break;
		}
		
		if(is_numeric($d1) && is_numeric($d2) && is_numeric($d3) 
			&& is_numeric($d4) && ($d5=='-') && is_numeric($d6) 
			&& is_numeric($d7) && ($d8=='-') && is_numeric($d9) && is_numeric($d10)){
				//$valor = $this->converter($valor,'DD/MM/YYYY','YYYY-MM-DD');
				return $valor;
				break;
		}
		
	}
	
	
	
}