<?php
include_once 'Zend/Db/Table/Abstract.php';

/**
 * Classe abstrata que extende Zend_Db_Table_Abstract;
 * 
 * Será utilizada toda vez que sejam necessários criar métodos com o usabilidade comum.
 *
 */
class Base_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	
	public function getNewValue()
	{
		$sql = "SELECT MAX({$this->_primary[1]}) as new_value FROM {$this->_schema}.{$this->_name}";
		//die($sql);
		$res = $this->getDefaultAdapter()->fetchRow($sql);
		//echo '<pre>';
		//print_r($res);
		//die;
		$new = 0;
		if (is_null($res['new_value'])) {
			$new = 1;
		} else {
			$new = $res['new_value'] +1;
		}

		return $new;

	}

	/**
	 * Método para criar combos.
	 *
	 * @param boolean $hasSelecione:	Caso seja true, faz com que o primeiro ítem tenha o valor
	 * 									null e o texto 'Selecione';
	 * @param string $fieldValue:	  	Nome do campo que será passado como chave; 
	 * @param string $fieldText:	  	Nome do campo que será passado como texto;
	 * @return unknown
	 */
	public function combo($hasSelecione = false, $fieldValue = null, $fieldText = null)
	{
		$fieldValue = ($fieldValue) ? $fieldValue : $this->_primary[1];
		$fieldText  = ($fieldText) ? $fieldText : $this->_cols[1];
		if ($hasSelecione === true) {
			$arr[''] = ' Selecione';
		}
		$res = $this->fetchAll();
		foreach ($res as  $valor) {
			$arr[$valor->$fieldValue] = ($valor->$fieldText);
		}
		return $arr;
	}

	public function combo2($hasSelecione = false, $fieldValue = null, $fieldText = null,$array = null)
	{
		$fieldValue = ($fieldValue) ? $fieldValue : $this->_primary[1];
		$fieldText  = ($fieldText) ? $fieldText : $this->_cols[1];
		if ($hasSelecione === true) {
			$arr[''] = ' Selecione';
		}
		$res = $this->fetchAll();
		foreach ($res as  $valor) {
			$arr[$valor->$fieldValue] = ($valor->$fieldText);
		}
		return $arr;
	}
	
	
	/**
	 * Método para criar Checkbox.
	 *
	 * @param boolean $hasSelecione:	Caso seja true, faz com que o primeiro ítem tenha o valor
	 * 									null e o texto 'Selecione';
	 * @param string $fieldValue:	  	Nome do campo que será passado como chave; 
	 * @param string $fieldText:	  	Nome do campo que será passado como texto;
	 * @return unknown
	 */
	public function checkbox($fieldValue = null, $fieldText = null)
	{
		$fieldValue = ($fieldValue) ? $fieldValue : $this->_primary[1];
		$fieldText  = ($fieldText) ? $fieldText : $this->_cols[1];
		
		$res = $this->fetchAll();
		foreach ($res as  $valor) {
			$arr[$valor->$fieldValue] = strtoupper($valor->$fieldText);
		}

		return $arr;
	}
	
	/**
	 * Método para criar um objeto de parâmetros e condição para efetuar algum comando dml de acordo
	 * com o descrito abaixo:
	 * 
	 * Caso o parâmetro $tipo = 'select':	Retorna um array apenas com parâmetros que podem ser
	 * 							enviados para o método fetchAll, fetch, etc, da classe Zend_Db_Table;
	 * 					$tipo = 'insert':	Retorna um array apenas com parâmetros que podem ser
	 * 							enviados para o método insert da classe Zend_Db_Table;
	 *					$tipo = 'update':	Retorna um array apenas com parâmetros que podem ser
	 * 							enviados para o método update da classe Zend_Db_Table juntamente
	 * 							com a condição necessária para tal de acordo com o campo $keys ou
	 * 							a chave primária da tabela setada na inicialização da classe que
	 * 							extende Zend_Db_Table. Ex: protected $_primary = 'cd_usuario';
	 * 					$tipo = 'delete':	Retorna a condição necessária para que o método delete da 
	 * 							classe Zend_Db_Table de acordo com o campo $keys ou a chave primária 
	 * 							da tabela setada na inicialização da classe que	extende Zend_Db_Table.
	 * 							Ex: protected $_primary = 'cd_usuario';
	 * 
	 * Caso o parâmetro $unsetEmpty =	true: executa o comando unset no parâmetro $arrDados caso o íten
	 * 									em questão seja nulo. Obs: Não funciona quando $tipo='update'; 
	 *
	 * @param array $arrDados: Dados do Request;
	 * @param boolean $unsetEmpty: Caso seja setado para true, garante que os ítens do array $arrDados 
	 * 							   sejam eliminados;
	 * @param string $type: Faz o tratamento do array $arrDados de acordo com a situação. 
	 * 						Pode ser: select, insert, update ou delete;
	 * @param array $keys: Caso seja passado, faz tratamento necessário para que o objeto de retorno
	 *    				   contenha em sua condição (where) todos os valores do array $keys.
	 * 					   Obs: Só utilizado para update e delete;
	 * @return array
	 */
	public function prepareRequest(array $arrDados, $unsetEmpty = false, $type='select', array $keys = null, $operadorChar = null){
		$this->_cols = $this->_getCols();
		$operadorChar = (!$operadorChar) ? 'LIKE' : $operadorChar; 
		$extensaoLike = ($operadorChar=='LIKE') ? '%' : null;
		
		
		$arrCols = '';
		foreach($this->_cols as $key=>$value){
			$arrCols[$value]='';
		}
		
		$arrCombine = array_intersect_key($arrDados, $arrCols);
		
		
		$Data = new Base_Controller_Action_Helper_Data();
		if($type == 'select' || $type == 'insert' || $type == 'delete'){
			if($unsetEmpty){
				foreach($arrCombine as $key=>$value){
					if(!$value){
						if($type=='insert' || $type=='update')
							$arrCombine[$key]=null;
						else{
							unset($arrCombine[$key]);
						}
					}	
				}
			}
		}
		if($type == 'select'){
			foreach($arrCombine as $key=>$value){
				if($this->_metadata[$key]['DATA_TYPE'] == 'varchar' || $this->_metadata[$key]['DATA_TYPE'] == 'varchar2'){
					$arrWhere[] = "UPPER({$this->_name}.{$key}) $operadorChar UPPER('{$value}$extensaoLike')";
				}
				elseif ((strpos($this->_metadata[$key]['DATA_TYPE'],'char')!==false) or (strpos($this->_metadata[$key]['DATA_TYPE'],'text')!==false)){
					$arrWhere[] = "{$this->_name}.{$key} $operadorChar '$extensaoLike{$value}$extensaoLike'";
				}
				else{
					if($value!='null'){
						if(is_array($value)){
							$value = implode(',',$value);
							$arrWhere[] = "{$this->_name}.{$key} in ({$value})";
						}else{
							$arrWhere[] = "{$this->_name}.{$key} = {$value}";
						}
						
					}else{
						$arrWhere[] = "{$this->_name}.{$key} is {$value}";
					}
				}
			}
			$arrFinal['where'] = isset($arrWhere) ? implode(" AND ", $arrWhere) : '';
			$arrFinal['params'] = $arrCombine;
			
			return $arrFinal;
		}
		if($type == 'insert'){
			$arrFinal['where'] = '';
			
//			echo "<pre>";
//			print_r($arrCombine);
//			echo "<BR>";
//			die(__FILE__."::".__LINE__);
			
//			if(isset($this->_primary[1]))
//			unset($arrCombine[$this->_primary[1]]);
			
			$arrFinal['params'] = $arrCombine;
			
			return $arrFinal;
		}
		elseif($type == 'update' || $type == 'delete'){
			if($keys){
				foreach($keys as $key=>$value){
					$arrKeys[] = "{$value} = {$arrCombine[$value]}";
				}
				
				$arrFinal['where'] = isset($arrKeys) ? implode(" AND ", $arrKeys) : '';
			}
			else{
				foreach($this->_primary as $key=>$value){
					if(isset($arrCombine[$value]) && $arrCombine[$value]){
						$arrKeys[] = "{$value} = {$arrCombine[$value]}";
					}
				}
				
				$arrFinal['where'] = isset($arrKeys) ? implode(" AND ", $arrKeys) : '';
			}
			
//			unset($arrCombine[$this->_primary[1]]);

			foreach($arrCombine as $a=>$b){
				if($b==""){
					$arrCombine[$a]=null;
				}
			}
			
			$arrFinal['params'] = $arrCombine;
			
			if(!$arrFinal['where']){
				return 'Erro ao criar condição(where)';
			}
			
			return $arrFinal;
		}
		
	}
	
	public function prepareWherePagination($arrDados){
		$this->_cols = $this->_getCols();
		
		
		$arrCols = '';
		foreach($this->_cols as $key=>$value){
			$arrCols[$value]='';
		}
		
		$arrCombine = array_intersect_key($arrDados, $arrCols);
		
		$arrTexto = array();
		foreach($arrCombine as $key=>$value){
			if($value)
				$arrTexto[]=$key."=".$value;
		}
		
		$retorno = implode("&",$arrTexto);
		if($retorno)
			$retorno = "&".$retorno;
		return $retorno;
	}
	
	
	
	/**
	 * Método default para operação de insert.
	 *
	 * @param array $arrDados
	 * @param boolean $unsetEmpty
	 * @param array $keys
	 * @return boolean
	 */
	public function salvar(array $arrDados, $unsetEmpty = false, array $keys = null){
		$arr = $this->prepareRequest($arrDados, $unsetEmpty, 'insert', $keys);
		if($lastId = $this->insert($arr['params'])){
			array_unshift($arr['params'],$lastId);
			return $arr['params'];
		}
		else{
			return false;
		}
	}
	/**
	 * Método default para operação de update.
	 *
	 * @param array $arrDados
	 * @param boolean $unsetEmpty
	 * @param array $keys
	 * @return boolean
	 */
	public function alterar(array $arrDados, $unsetEmpty = false, array $keys = null){
		$arr = $this->prepareRequest($arrDados, $unsetEmpty, 'update', $keys);
		if($this->update($arr['params'],$arr['where'])){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Método default para operação de delete.
	 *
	 * @param unknown_type $arrDados
	 * @param unknown_type $unsetEmpty
	 * @param unknown_type $keys
	 * @return boolean
	 */
	public function excluir($arrDados, $unsetEmpty = false, $keys = null){
		$arr = $this->prepareRequest($arrDados, $unsetEmpty, 'delete', $keys);
		if($this->delete($arr['where'])){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Método default para contar a quantidade de registros na tabela.
	 *
	 * @param string $where
	 * @return integer
	 */
	public function _count($where = null){
		//$sql = "SELECT MAX({$this->_primary[1]}) as new_value FROM {$this->_schema}.{$this->_name}";
		$sql="SELECT count({$this->_primary[1]}) as qtd FROM {$this->_schema}.{$this->_name}";
		
		if($where){
			$sql .= " where ".$where." ";			
		}
		
		$rs = $this->getDefaultAdapter()->fetchRow($sql);
		return $rs['qtd'];
	}
	
	/**
	 * Método default para Construir o Limit de dados para a grid
	 *
	 * @param string $page
	 * @param string $limit
	 * @return string
	 */
	public function constructLimit($page = null, $limit = null){

		if($this->_db instanceof Zend_Db_Adapter_Pdo_Oci){
			
		}else{
			if(isset($page) and is_numeric($page)) {			
				return " LIMIT ".($limit)." OFFSET ".(($page-1)*$limit);
			}
			else {
				return " LIMIT ".$limit;
			}
		}
	}
	
	
	/**
	 * Método default para Buscar todos os registros de Uma Tabela
	 *
	 * @param string $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return string
	 */
	public function getAll($arrDados, $arrPagination){
		
		$arr = $this->prepareRequest($arrDados, true, 'select');
		$where = $arr['where'] ? "{$arr['where']}" : null;
		
		$order = $arrPagination['order'] ? $arrPagination['order'] . " " .$arrPagination['direction'] : null; 
		
		
		$select = $this->select();
		$select->from(array($this->_name=>$this->_schema.'.'.$this->_name))
			   ->order($order)
			   ->limitPage($arrPagination['page'],$arrPagination['limit']);		
		
		if($where)
			$select->where($where);
			   
		$res = $this->fetchAll($select)->toArray();
		return $res;
		
	}
	
	public function count($arrDados){
		
		$arr = $this->prepareRequest($arrDados, true, 'select');
		$where = $arr['where'] ? "{$arr['where']}" : null;
		$select = $this->select();
		$select->from(array($this->_name=>$this->_schema.'.'.$this->_name),array('_count' => 'COUNT(1)'));
		
		if($where)
			$select->where($where);
			   
		$res = $this->fetchAll($select)->toArray();
		return $res[0]['_count'];
		
	}
	
	function getInformation($arrDados){
		echo "<pre>";
		print_r($arrDados);
		die("<hr>Arquivo: ".__FILE__."<BR>Linha: ".__LINE__);
	}
	
	public function prepareStringWhere($arrDados){
		$arrCols = '';
		foreach($this->_cols as $key=>$value){
			$arrCols[$value]='';
		}
		$arrCombine = array_intersect_key($arrDados, $arrCols);

		foreach($arrDados['join'] as $key=>$value){
			$arrCombine[$key]=$value;
		}
	
		foreach($arrCombine as $key=>$value){
			if(isset($this->_metadata[$key])){
				if($this->_metadata[$key]['DATA_TYPE'] == 'varchar' || $this->_metadata[$key]['DATA_TYPE'] == 'varchar2'){
					$arrWhere[] = "UPPER({$this->_name}.{$key}) LIKE UPPER('{$value}%')";
				}
				elseif (strpos($this->_metadata[$key]['DATA_TYPE'],'char')!==false){
					$arrWhere[] = "{$this->_name}.{$key} like '%{$value}%'";
				}
				else{
					$arrWhere[] = "{$this->_name}.{$key} = {$value}";
				}
			}else{
				$expKey = explode(".",$key);
				if(count($expKey)>1){
					$arrWhere[] = "{$expKey[0]}.{$expKey[1]} = '{$value}'";
				}else{
					$arrWhere[] = "{$expKey[1]} = '{$value}'";
				}
			}
		}
		
		$arrFinal['where'] = isset($arrWhere) ? implode(" AND ", $arrWhere) : '';
		$arrFinal['params'] = $arrCombine;
		return $arrFinal;
	}
	
	public function SqlExec($sql, $where=null, $order=null,$limit=null,$join = null, $groupBy = null, $wherein = null,$union = null){
		//$sql = $sql.$this->_schema.'.'.$this->_name." ".$join." ".$where." ".$order." ".$limit; 
		$sql = "select * from (".$sql.$this->_schema.'.'.$this->_name." ".$join." ".$wherein." ".$groupBy." ".$union." ) a ".$where." ".$order." ".$limit; 
		$res = $this->getAdapter()->fetchAll($sql);
		return $res;
		
	}	

		
	/*
	 * Método criado para montar o Where vindo do Data table
	 * Irei colocá-lo dentro da Absctract para que todos possam usar.
	 */
	public function trataRequestDataTables($arrayRequest){
		if(isset($arrayRequest['busca_geral']) and $arrayRequest['busca_geral']!=''){
                    
                    $arrayChar = array('varchar','text','character','bpchar');
                    $arrayNum = array('integer','numeric','decimal','int4','int8');
			
			$split = explode(' ',$arrayRequest['busca_geral']);
			$arrParamsBusca[] = $arrayRequest['busca_geral'];
			foreach ($split as $k=>$v){
				$arrParamsBusca[] = $v;
			}
			
			$arraySelect = array();
			foreach($arrParamsBusca as $k=>$v){
				foreach($this->_cols as $key=>$value){
                                    if(isset($this->_metadata[$value]['DATA_TYPE']) and in_array($this->_metadata[$value]['DATA_TYPE'],$arrayChar)){
						$arraySelect[] = $this->_name.'.'.$value." ilike '%".$v."%'";
                                        }else if(isset($this->_metadata[$value]['DATA_TYPE']) and in_array($this->_metadata[$value]['DATA_TYPE'],$arrayNum) and is_numeric($value)){
						$arraySelect[] = $this->_name.'.'.$value." = ".$v;
					}
				}
			}
			return $arraySelect;
		}
		return null;	
	}

	/*
	 * Método criado para montar o Where vindo do Data table por coluna
	 * 
	 */
	public function trataRequestDataTablesCols($arrayRequest){
		
		$arrayChar = array('varchar','text','character','bpchar');
		$arrayNum = array('integer','numeric','decimal','int4','int8');
		$arrayDate = array('timestamp','date');
		$arraySelect = array();

		if(isset($arrayRequest['searchCol']) and count($arrayRequest['searchCol']) >0 ){
			foreach($arrayRequest['searchCol'] as $key=>$value){
				
				if(isset($this->_metadata[$key]['DATA_TYPE']) and in_array($this->_metadata[$key]['DATA_TYPE'],$arrayChar)){
					$value= preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $value ) );
					$arraySelect[] = $this->_name.'.'.$key." ilike '%".$value."%'";
				}else if(isset($this->_metadata[$key]['DATA_TYPE']) and in_array($this->_metadata[$key]['DATA_TYPE'],$arrayNum) and is_numeric($value)){
					$arraySelect[] = $this->_name.'.'.$key." = ".$value;
				}else if(isset($this->_metadata[$key]['DATA_TYPE']) and in_array($this->_metadata[$key]['DATA_TYPE'],$arrayDate)){
					$arraySelect[] = $this->_name.'.'.$key."::text like '%".$value."%'";
				}else if (isset($this->_metadata[$key])){
					
				}
			}
			
			return $arraySelect;
		}
		return null;	
	}
}