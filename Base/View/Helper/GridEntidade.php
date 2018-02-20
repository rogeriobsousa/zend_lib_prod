<?php
class Base_View_Helper_GridEntidade extends Base_View_Helper_Grid {
	
	public function gridEntidade(array $parametros,$paginacao,$aux = null){
		$this->wherePagination = $parametros['wherePagination'];
		$this->casoDeUso = $parametros['casoDeUso'];
		$this->limit = $parametros['limit'];
		$this->direction = $parametros['direction'];
		$this->path = $parametros['path'];
		$grid = $this->openTableHtml($this->casoDeUso,$parametros['tituloGrid'],$parametros['tituloColunas'],$aux);
		$grid .= $this->makeGrid($this->casoDeUso,$parametros['tituloColunas'],$parametros['chaves'],$parametros['dados'],$parametros['path'],$aux);
		$grid .= $this->makePager($parametros['path'],$paginacao,$this->limit);
		$grid .= $this->closeDivHtml();
		echo $grid;
	}
	
	public function makeGrid($casoDeUso,$tituloColunas,$chaves,$dados,$path,$aux = null){

        $chavesFlip = array_flip($chaves);
		//$chaves = array_flip($chaves);
		$class="class=\"even\"";
		$htmlGrid = "<tbody>";

        foreach($dados as $dd){
			$class=($class=="")?"class=\"even\"":"";
			$id = implode(",",array_intersect_key($dd,$chavesFlip));
			//$htmlGrid .= "<tr {$class}>";

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

			/*$htmlGrid .= "<td width='15'><img id='imgExcluir'
								height='13' width='13' 
								onclick=\"_delete('{$this->casoDeUso}','{$chaves[0]}={$dd[$chaves[0]]}','Deseja Excluir este Registro???')\" 
								style=\"cursor:pointer;\" src=\"$path/public/img/del.png\" 
								alt=\"Excluir\"/></td>";*/

            
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
                    //$htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getDataCallBack('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}','showContatos(\'{$dd[$chaves[0]]}\')');\">".$value."</td>";
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
                        //$htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getData('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}')\">".$value."</td>";
	                    
	                    //ATENÇÃO... esta linha de comando foi feita para além de capturar os dados da entidade, capturar também os dados dos contatos e colocar na grid de contatos.
	                    //By Sombrero
                        $htmlGrid .= "<td style=\"cursor:pointer;\" onclick=\"getDataCallBack('$casoDeUso','{$chaves[0]}={$dd[$chaves[0]]}','showContatos(\'{$dd[$chaves[0]]}\')');\">".$value."</td>";
                    }
                }
			}
			$htmlGrid .= "</tr>";
			
		}
		
		$htmlGrid .= "</tbody></table>";
		return $htmlGrid;
	}
}

?>