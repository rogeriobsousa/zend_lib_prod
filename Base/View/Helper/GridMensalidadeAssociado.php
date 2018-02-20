<?php
class Base_View_Helper_GridMensalidadeAssociado
{
	protected $tituloGrid     = "Grid Default";
	protected $tituloColunas = "";
	protected $chaves = "";
	protected $modulo = "";
	protected $dados = "";
	protected $limit = "";
	protected $path = "";
	protected $casoDeUso = "";
	protected $page = "";
	protected $direction = "";
	protected $wherePagination = "";
	protected $divRetorno = "";

	public function gridMensalidadeAssociado(array $parametros,$paginacao,$aux = null){
		$this->wherePagination = $parametros['wherePagination'];
		$this->casoDeUso = $parametros['casoDeUso'];
		$this->limit = $parametros['limit'];
		$this->direction = $parametros['direction'];
		$this->path = $parametros['path'];
		$this->modulo = $parametros['modulo'];
		$this->divRetorno = $parametros['divRetorno'];
		
		$grid = $this->openTableHtml($this->casoDeUso,$parametros['tituloGrid'],$parametros['tituloColunas'],$aux);
		$grid .= $this->makeGrid($this->casoDeUso,$parametros['tituloColunas'],$parametros['chaves'],$parametros['dados'],$parametros['path'],$aux);
		$grid .= $this->closeDivHtml();
		$grid .= $this->makePager($parametros['path'],$paginacao,$this->limit,$parametros);
		echo $grid;
	}

	protected function openTableHtml($casoDeUso,$tituloGrid,$tituloColunas,$aux = null){
		$tableScript = "<div class='grid'>
						<table id='table".ucwords($casoDeUso)."' style='width:100%'>
						<thead>
						<tr>
						";
        if($aux == null){
            $tableScript .= "<th>&nbsp;</th>";
        }else{
            //retira do cabeÃ§alho o espaÃ§o destinado para o Ã­cone de exclusÃ£o
            $tableScript .= "";
        }

        foreach($tituloColunas as $key=>$value){
			$image = "";
			if($value){
				$image =  $this->path."/public/img/{$this->direction}.gif";
			$tableScript .= "<th class='{$this->direction}'
			onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','order=$key{$this->wherePagination}&limit=".$this->limit."&direction='+this.className)\">
			<img src=\"$image\" style=\"cursor:pointer;\">{$value}</th>";
			}
			
		}

		$tableScript .= "</tr></thead>";
		return $tableScript;
	}

	protected function makeGrid($casoDeUso,$tituloColunas,$chaves,$dados,$path,$aux = null){
        
        $chavesFlip = array_flip($chaves);
		//$chaves = array_flip($chaves);
		$class="class=\"even\"";
		$htmlGrid = "<tbody>";

        foreach($dados as $dd){
			$class=($class=="")?"class=\"even\"":"";
			$id = implode(",",array_intersect_key($dd,$chavesFlip));

            
            if($aux == null){
                $htmlGrid .= "<tr {$class}>";
                $htmlGrid .= "<td width='15'><!--<img id='imgExcluir'
								height='13' width='13'
								onclick=\"_delete('{$this->casoDeUso}','{$chaves[0]}={$dd[$chaves[0]]}','Deseja Excluir este Registro???')\"
								style=\"cursor:pointer;\" src=\"$path/public/img/del.png\"
								alt=\"Excluir\"/>--></td>";
            }else{
                $htmlGrid .= "";
            }

            //LEMBRAR DE COLOCAR A BASE URL
			$arrfinal = array();
			foreach($tituloColunas as $key=>$value){
				if(array_key_exists($key,$dd)){
					$arrfinal[$key]=$dd[$key];
				}
			}

            //colocar a primary_key
			foreach($arrfinal as $key=>$value){
                if(array_key_exists($key,$tituloColunas)){
                    //verifica se hÃ¡ algum input nos values
					if($key=='ni_mes'){
						$value = $this->getMes($value);
					}
					
					if($key=='nf_valor_pago'){
						$valor=$value;
						$value .= "<input maxlength='7' onkeypress='mascara(this,moeda)' type='text' name='nf_valor_pago_{$dd['cd_associado']}_{$dd['ni_ano']}_{$dd['ni_mes']}' id='nf_valor_pago_{$dd['cd_associado']}_{$dd['ni_ano']}_{$dd['ni_mes']}' size='8' value='$value' style='display:none'>";
					}

					if($key=='st_pagamento'){
						if($value=='S'){
							//$value = "<input type='button' value='Pagamento' name='st_pagamento' id='st_pagamento_{$dd[$chaves[0]]}_{$dd['cd_associado']}_{$dd['ni_ano']}_{$dd['ni_mes']}' onclick=\"pagamento('{$dd['cd_associado']}|{$dd['ni_ano']}|{$dd['ni_mes']}',this.id)\">";
							$value = "<img src='$path/public/img/print32.png' width='16' height='16' title='Imprimir Recibo' onclick=\"recibo('{$dd['cd_associado']}|{$dd['ni_ano']}|{$dd['ni_mes']}')\">";
							//$value = 'Sim';
							//colocar a check box;
						}else{
							if($_SESSION['ss_cd_perfil']==1){
							$value = "<img onclick=\"salvarPagamento('{$dd['cd_associado']}|{$dd['ni_ano']}|{$dd['ni_mes']}')\" id='img_st_pagamento_{$dd['cd_associado']}_{$dd['ni_ano']}_{$dd['ni_mes']}' src='$path/public/img/add.png' style='display:none;'>
							<input style='height:auto;' type='button' value='Lançar Pagamento' name='st_pagamento' id='st_pagamento_{$dd['cd_associado']}_{$dd['ni_ano']}_{$dd['ni_mes']}' onclick=\"pagamento('{$dd['cd_associado']}|{$dd['ni_ano']}|{$dd['ni_mes']}',this.id)\">";
							}else{
							$value = 'Pendente'; 	
							}//$value = 'Não';
						}
					}
                	//$htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getData('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}','{$this->modulo}','{$this->divRetorno}')\">".$value."</td>";
                	$htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"\">".$value."</td>";
				}
			}
			
			$htmlGrid .= "</tr>";

		}

		$htmlGrid .= "</tbody></table>
		<script>
		
		function recibo(valor){
			var split = valor.split('|');
			var cd_associado = split[0];
			var ni_ano = split[1];
			var ni_mes = split[2];
			
			window.open('modulo-academicos/mensalidade/recibo-pagamento?c='+cd_associado+'&a='+ni_ano+'&m='+ni_mes,'janela1','width=600,height=650,scrollbars=YES') 
			
			
			
		}
		
		
		function pagamento(valor,id){
			var split = valor.split('|');
			var cd_associado = split[0];
			var ni_ano = split[1];
			var ni_mes = split[2];
			
			//alert('nf_valor_pago_'+cd_associado+'_'+ni_ano+'_'+ni_mes)
			$('#nf_valor_pago_'+cd_associado+'_'+ni_ano+'_'+ni_mes).show();
			$('#st_pagamento_'+cd_associado+'_'+ni_ano+'_'+ni_mes).hide();
			$('#st_pagamento_'+cd_associado+'_'+ni_ano+'_'+ni_mes).hide();
			$('#img_st_pagamento_'+cd_associado+'_'+ni_ano+'_'+ni_mes).show();
			
			
				
		}
		
		function salvarPagamento(valor){
			var split = valor.split('|');

			var cd_associado = split[0];
			var ni_ano = split[1];
			var ni_mes = split[2];
			var nf_valor_pago = $('#nf_valor_pago_'+cd_associado+'_'+ni_ano+'_'+ni_mes).val();
			var params = 'st_pagamento=true&cd_associado='+cd_associado+'&ni_ano='+ni_ano+'&ni_mes='+ni_mes+'&nf_valor_pago='+nf_valor_pago;
			 
			if(!nf_valor_pago){
				alert('Informe o Valor');
				return;
			}
			
			$.ajax({
				type: \"POST\",
				url: $('#appPath').val()+\"/modulo-academicos/associado/salvar-pagamento\",
				data:params,
				success: function(retorno){
					$('#lado_direito').load($('#appPath').val()+\"/modulo-academicos/mensalidade/controle-mensalidade\",{cd_associado: cd_associado});
				},
				error: function(){
					alert('erro')
				}
			});
			
		
			
			
			
		}
		</script>
		";
		return $htmlGrid;

	}

	protected function makePager($path,$paginacao,$limit, array $parametros = null){
		$arrOpt = array('5'=>'5','10'=>'10','20'=>'20','30'=>'30','40'=>'40');
		$option = "";
		foreach($arrOpt as $key=>$value){
			$selected = "";
			if($key==$limit){
				$selected = "selected='selected'";
			}
			$option .= "<option value='$key' $selected>$value</option>";
		}
		$gridHtml = "<div id='pager' class='content_header'>
						<form id='paginationForm'>
							<span class='content_title' >  </span>
							<div class='content_search' style='margin-left:5px;'>
								<label >&nbsp;</label>
								<!--<select id='limit' onchange=\"grid('$this->casoDeUso',null,'{$this->modulo}','page=1{$this->wherePagination}&limit='+this.value)\">
									$option
								</select>-->
							</div>
							$paginacao
						</form>
					</div>";
		//$gridHtml = "<input type='hidden' id='limit' name='limit' value='10'>";
		return $gridHtml;
	}

	protected function closeDivHtml(){
		$gridHtml ="</div>";

		return $gridHtml;
	}





public function getMes($mes){
		$mesTexto = '';
		switch($mes){
			case 1:
				$mesTexto = 'Janeiro';
			break;	
			case 2:
				$mesTexto = 'Fevereiro';
			break;	
			case 3:
				$mesTexto = 'Março';
			break;	
			case 4:
				$mesTexto = 'Abril';
			break;	
			case 5:
				$mesTexto = 'Maio';
			break;	
			case 6:
				$mesTexto = 'Junho';
			break;	
			case 7:
				$mesTexto = 'Julho';
			break;	
			case 8:
				$mesTexto = 'Agosto';
			break;	
			case 9:
				$mesTexto = 'Setembro';
			break;	
			case 10:
				$mesTexto = 'Outubro';
			break;	
			case 11:
				$mesTexto = 'Novembro';
			break;	
			case 12:
				$mesTexto = 'Dezembro';
			break;	
		}
		return $mesTexto;
	}
















}
?>