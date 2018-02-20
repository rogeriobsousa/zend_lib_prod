<?php
class Base_View_Helper_GridAssociado
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

	public function gridAssociado(array $parametros,$paginacao,$aux = null){
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
		$grid .= $this->makePager($parametros['path'],$paginacao,$this->limit);
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
                $htmlGrid .= "<td width='15'>
                				<!--<img id='imgExcluir'
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
                        $htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getData('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}&{$chaves[1]}={$dd[$chaves[1]]}','{$this->modulo}','{$this->divRetorno}')\">".$value."</td>";
                    }
				}
			}
			
			$htmlGrid .= "</tr>";

		}

		$htmlGrid .= "</tbody></table>";
		return $htmlGrid;

	}

	protected function makePager($path,$paginacao,$limit){
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
							<span class='content_title' >Resultados por página:  </span>
							<div class='content_search' style='margin-left:5px;'>
								<label >&nbsp;</label>
								<select id='limit' onchange=\"grid('$this->casoDeUso',null,'{$this->modulo}','page=1{$this->wherePagination}&limit='+this.value)\">
									$option
								</select>
							</div>
							$paginacao
						</form>
					</div>";

		return $gridHtml;
	}

	protected function closeDivHtml(){
		$gridHtml ="</div>";

		return $gridHtml;
	}






















}
?>