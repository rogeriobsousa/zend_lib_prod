<?php
class Base_View_Helper_GridChamadoCliente extends Base_View_Helper_Grid
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

	public function gridChamadoCliente(array $parametros,$paginacao,$aux = null){
		$this->wherePagination = $parametros['wherePagination'];
		$this->casoDeUso = $parametros['casoDeUso'];
		$this->limit = $parametros['limit'];
		$this->direction = $parametros['direction'];
		$this->path = $parametros['path'];
		$this->modulo = $parametros['modulo'];
		$this->divRetorno = $parametros['divRetorno'];
		$grid = $this->openTableHtml($this->casoDeUso,$parametros['tituloGrid'],$parametros['tituloColunas'],$aux);
		$grid .= $this->makeGrid($this->casoDeUso,$parametros['tituloColunas'],$parametros['chaves'],$parametros['dados'],$parametros['path'],$aux);
		$grid .= $this->makePager($parametros['path'],$paginacao,$this->limit);
		$grid .= $this->closeDivHtml();
		echo $grid;
	}
	
	protected function openTableHtml($casoDeUso,$tituloGrid,$tituloColunas,$aux = null){
		$tableScript = "<h5>{$tituloGrid}</h5>
						<div class='grid'>
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
			$tableScript .= "<th  class='{$this->direction}'
			onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','order=$key{$this->wherePagination}&limit=".$this->limit."&direction='+this.className)\">
			<img src=\"$image\" style=\"cursor:pointer;\">{$value}</th>";
			}
			
		}

		$tableScript .= "<th></th><th></th></tr></thead>";
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
                $htmlGrid .= "<td width='15'><img id='imgExcluir'
								height='13' width='13'
								onclick=\"_delete('{$this->casoDeUso}','{$chaves[0]}={$dd[$chaves[0]]}','Deseja Excluir este Registro???')\"
								style=\"cursor:pointer;\" src=\"$path/public/img/del.png\"
								alt=\"Excluir\"/></td>";
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
                    if($key == "checkbox"){
                        if($value == 'true'){
                            $checked = 'checked';
                        }else if($value == 'false'){
                            $checked = '';
                        }
                        $htmlGrid .= "<td align=\"center\">
                                          <input type='checkbox' name='check[]' id='check' $checked value='{$dd[$chaves[0]]}'>
                                      </td>";
                    }else{
                        $htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getData('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}','{$this->modulo}','{$this->divRetorno}')\">".$value."</td>";
                    }
				}
			}
			
			$htmlGrid .= "<td width='15'>
								<img src=\"public/images_widestar/Document_peq.png\" onclick=\"getData('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}','{$this->modulo}','{$this->divRetorno}','view')\"
								style=\"cursor:pointer;\"/></td>";
			
			
			switch($dd['cd_status_chamado']){
				default:
					$htmlGrid .= "<td width='15'></td>";
					$htmlGrid .= "<td width='15'></td>";
				break;
				case 'ENCT':
					$htmlGrid .= "<td width='15'>
									<img src=\"public/images_widestar/Tick_peq.png\" onclick=\"concluirChamado('cd_chamado={$dd['cd_chamado']}')\"
									style=\"cursor:pointer; float:left;\" title=\"Encerrar Chamado\"/>
									</td>";	
					$htmlGrid .= "<td width='15'>
									<img src=\"public/images_widestar/Restart_peq.png\" onclick=\"encerrarChamado('cd_chamado={$dd['cd_chamado']}')\"
									style=\"cursor:pointer; float:left\" title=\"Reabrir Chamado\"/>
									</td>";	
				break;
			}
			
			
			
			
			$htmlGrid .= "</tr>";

		}

		$htmlGrid .= "</tbody></table>";
		return $htmlGrid;

	}




}
?>