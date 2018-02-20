<?php
class Base_View_Helper_Datediff
{
	public function datediff($date1, $date2)
	{
		$obj = new Util_Datediff($date1, $date2);
		
		return $obj->datediff();
	}

	
	
	
	/**
	 * Método que defini um campo data e hora para o sistema com 
	 * validação e componete de calendario.
	 * 
	 * @author Wunilberto Melo
	 * @since 17/10/2008
	 * @param text $dataInicio  Defini a data inicio
	 * @param text $dataFim     Defini a segunda data
	 * @return text $botaoData Retorna o script na tela para fazer as datas 
	 */
	public function comparaDataInicioFim($dataInicio, $dataFim)
	{
		echo " <script>
					$(document).ready(function(){
						$('#{$dataFim}').bind('blur', function(){
							var nomeInicio = $('#{$dataInicio}').val();
							var nomeFim    = $('#{$dataFim}').val();
							if(nomeInicio && nomeFim){
								var dataFim = parseInt(nomeFim.split( \"/\" )[2].toString()+nomeFim.split( \"/\" )[1].toString()+nomeFim.split( \"/\" )[0].toString()); 
								var dataInicio = parseInt( nomeInicio.split( \"/\" )[2].toString()+nomeInicio.split( \"/\" )[1].toString()+nomeInicio.split( \"/\" )[0].toString()); 
	
								if(dataFim < dataInicio){
									alert('Data final menor que a de inicio.');
									$('#{$dataFim}').focus()
									$('#{$dataFim}').select()
		  						} 
							}
						});
					});
				</script>";
	}
	
	/**
	 * Método que defini um campo data e hora para o sistema com 
	 * validação e componete de calendario.
	 * 
	 * @author Wunilberto Melo
	 * @since 17/10/2008
	 * @param text $dataInicio  Defini a data inicio
	 * @param text $dataFim     Defini a segunda data
	 * @return text $botaoData Retorna o script na tela para fazer as datas 
	 */
	public function comparaDataHoraInicioFim($dataHoraInicio, $dataHoraFim)
	{
		echo " <script>
					$(document).ready(function(){
						$('#{$dataHoraFim}').bind('blur', function(){
							var dataHoraInicio = $('#{$dataHoraInicio}').val();
							var dataHoraFim    = $('#{$dataHoraFim}').val();
							if(dataHoraInicio != '' && dataHoraFim != ''){
								var dataInicio     = dataHoraInicio.split( \" \" )[0].toString();
								var horaInicio     = dataHoraInicio.split( \" \" )[1].toString();
								var dataFim        = dataHoraFim.split( \" \" )[0].toString();
								var horaFim        = dataHoraFim.split( \" \" )[1].toString();
								var dataInicioInt = parseInt( dataInicio.split( \"/\" )[2].toString()+dataInicio.split( \"/\" )[1].toString()+dataInicio.split( \"/\" )[0].toString()); 
								var dataFimInt    = parseInt( dataFim.split( \"/\" )[2].toString()+dataFim.split( \"/\" )[1].toString()+dataFim.split( \"/\" )[0].toString()); 
								
								var horaInicial    = parseInt( horaInicio.split( \":\" )[0].toString());
								var minutoInicio  = parseInt( horaInicio.split( \":\" )[1].toString());
								var segundoInicio = parseInt( horaInicio.split( \":\" )[2].toString());
								var horaFinal    = parseInt( horaFim.split( \":\" )[0].toString());
								var minutoFim  = parseInt( horaFim.split( \":\" )[1].toString());
								var segundoFim = parseInt( horaFim.split( \":\" )[2].toString());
								if(dataFimInt < dataInicioInt){
									alert('Data Inicio maior que a data Fim!');
									$('#{$dataHoraFim}').focus();
									$('#{$dataHoraFim}').select();
								} else if(horaFinal < horaInicial){
									alert('Hora Inicio maior que a hora Fim!');
									$('#{$dataHoraFim}').focus();
									$('#{$dataHoraFim}').select();
								} else if(minutoFim < minutoInicio){
									alert('Minuto Inicio maior que a minuto Fim!');
									$('#{$dataHoraFim}').focus();
									$('#{$dataHoraFim}').select();
								} else if(segundoFim < segundoInicio){
									alert('Segundo Inicio maior que o segundo Fim!');
									$('#{$dataHoraFim}').focus();
									$('#{$dataHoraFim}').select();
								}
							}
						});
					});
				</script>";
	}
}