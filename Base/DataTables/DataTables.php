<?php



class Base_DataTables_DataTables{
	
	static function filterEspec ( $request)
	{
            if(isset($request['parametros'])){
                    $arrTextoParams = '';
			
                    foreach($request['parametros'] as $key=>$value){
                    	$pos = strpos($key, 'tx_');
                        if($value!='' and $value!='Selecione'){
                        	if(is_numeric($value) and $pos===false){
	                        	$arrChave = explode('.',$key);
	                            $arrTextoParams[] = $arrChave[1]." = ".$value;
                        	}else{
	                        	$arrChave = explode('.',$key);
	                            $arrTextoParams[] = $arrChave[1]." ilike '%".$value."%'";
                        	}
                        }
                    }
                   
                    if(is_array($arrTextoParams)){
                            $textoParams = implode(' and ',$arrTextoParams);
                            return "( ".$textoParams." ) ";
                    }else{
                        return null;
                    }
			}		
            		
	}
	
	
	static function MultiValid ( $request)
	{
		if(isset($request['multi_parametros'])){
			$arrTextoParams = '';
			
			foreach($request['multi_parametros']['tabelas'] as $key=>$value){
				
				$arrSplit = explode('.',$key);
				
				foreach($value as $v){
					switch ($request['multi_parametros']['tipo']){
						case 'like':
							$arrTextoParams[] = $arrSplit[1]." ilike '%$v%'";
							
							break;
						default:
							$arrTextoParams[] = $arrSplit[1]." = '$v'";
							break;
					}
				}
			}
			
			if(is_array($arrTextoParams)){
				switch(strtoupper($request['multi_parametros']['op'])){
					case 'OR':
							$textoParams = implode(' or ',$arrTextoParams);
	            			return "( ".$textoParams." ) ";
						break;
					default:
							$textoParams = implode(' and ',$arrTextoParams);
	            			return "( ".$textoParams." ) ";
						break;
				}
				
            }else{
            	return null;
            }
		}	
	}
	
	
	static function getDados ( $request, $obj, $table, $primaryKey, $columns, $join = false, $pivotKey = null, $groupBy = null)
	{
		$arrWhere = array();
		$bindings = array();
		// Build the SQL query string from the request
		$limit = self::limit( $request, $columns );
		$order = self::order( $request, $columns );
		$where = self::filter( $request, $columns, $bindings );

		
		$w1 = self::filterEspec($request);
		$w2 = self::MultiValid($request);
		
		if($w1){
			$arrWhere[] = $w1;	
		}
		if($w2){
			$arrWhere[] = $w2;	
		}
		
		end($columns); 
		$ultimachave = key($columns);
		$arrColunasAdicionadas = array();
               
		if(isset($request['parametros'])){
			foreach($request['parametros'] as $key=>$value){
				if($value!=''){
					$arrChave = explode('.',$key);
					
					$hasChave = false;
					foreach($columns as $k=>$v){
						if($v['db']==$arrChave[1]){
							$hasChave = true;
						}
					}
												   
					if(!$hasChave){
						$ultimachave ++;
						$columns[$ultimachave]['db'] = $arrChave[1];
						$columns[$ultimachave]['dt'] = $ultimachave;
						$columns[$ultimachave]['subquery'] = $key;
						$arrColunasAdicionadas[] = $ultimachave;
					}
				}
            }
		}
		
		
		$whereEspec = implode(' and ',$arrWhere);

		if($where){
			if($whereEspec!=''){
				$addWhere = " and ".$whereEspec;
				$where = $where.$addWhere;
			}
		}else{
			if ($whereEspec){
				$where = " where ".$whereEspec;
			}
		}
		
		
		if($pivotKey){
			if($where){
				$where .= "and ".$pivotKey['chave']." = ".$pivotKey['valor'];
			}else{
				$where = " WHERE ".$pivotKey['chave']." = ".$pivotKey['valor'];
			}
			
			if(isset($pivotKey['espec'])){
				$where .= " and ".$pivotKey['espec'];
			}
		}
		$union = "";
//		$union = "union
//					select uu.cd_usuario as cd_usuario, uu.tx_path_foto_usuario as tx_path_foto_usuario, uu.tx_nome_usuario as tx_nome_usuario, uu.st_sexo as st_sexo 
//					from siacom2.s_usuario  uu
//					where cd_tipo_usuario = 1";
//		
		$objDB = new $obj();
		
		if($groupBy){
			$groupBy = " group by ".$groupBy;
		}
		
		$data = $objDB->SqlExec('select '.implode(", ", self::pluck($columns, 'db')).' from ', $where, $order ,$limit,$join,$groupBy,null,$union);
		// Data set length after filtering
		//$resFilterLength = $objDB->SqlExec("SELECT count(*) from ", $where, null, null, $join);

		$counter = $objDB->SqlExec('select '.implode(", ", self::pluck($columns, 'db')).' from ', $where, $order ,null,$join,$groupBy);
		
		//$recordsFiltered = $resFilterLength[0]['count'];
		$recordsFiltered = count($counter);
		// Total data set length
		
		
		$whereCount = '';
		$pivot = '';
		$groupBy = '';
		if($pivotKey){
			$whereCount = " WHERE ".$pivotKey['chave']." = ".$pivotKey['valor'];
			
			$pivot = $pivotKey['chave'].', ';
			$groupBy = " group by ".$pivotKey['chave'];
		}
		
		$wherein = '';
		if(isset($pivotKey['espec'])){
			$wherein = " where ".$pivotKey['espec'];
		}
	
		$resTotalLength = $objDB->SqlExec("SELECT $pivot COUNT({$primaryKey}) from ",$whereCount, null, null, $join, $groupBy, $wherein);
		
		if(count($resTotalLength)>0){
			$recordsTotal = $resTotalLength[0]['count'];
		}else{
			$recordsTotal = count($resTotalLength);
		}
		/*
		 * Output
		 */
//		if($request['search']['value']==''){
//			$recordsFiltered = $recordsTotal;
//		}
		
		if(isset($arrColunasAdicionadas))
		foreach($arrColunasAdicionadas as $key=>$value){
			unset($columns[$value]);
		}
		
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $data )
		);
	}
	
	static function limit ( $request, $columns )
	{
		$limit = '';

		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval($request['length'])." OFFSET ".intval($request['start']);
		}
		return $limit;
	}
	
	static function order ( $request, $columns )
	{
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';

					$orderBy[] = $column['db'].' '.$dir;
				}
			}

			$order = 'ORDER BY '.implode(', ', $orderBy);
		}

		return $order;
	}
	
	static function filter ( $request, $columns, &$bindings )
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );

		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );

				//echo "Indice: ".$columnIdx."<BR>";
				
				$column = $columns[ $columnIdx ];
				//echo "Coluna: ".print_r($column)."<BR>";

				if ( $requestColumn['searchable'] == 'true' ) {
					if(isset($column['type']) and $column['type']=='int'){
						if(is_int($str)){
							$binding = self::bind( $bindings, $str, PDO::PARAM_INT);
							$globalSearch[] = "".$column['db']." = ".$binding;
						}
						
					}else{
						$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                                                if(isset($column['tipo_original']) and $column['tipo_original']=='text'){
                                                    $globalSearch[] = "".$column['db']."::text ILIKE ".$binding;
                                                }else{
                                                    $globalSearch[] = "".$column['db']." ILIKE ".$binding;
                                                }						
					}
					
				}
			}
		}

		// Individual column filtering
		for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $request['columns'][$i];
			$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $columns[ $columnIdx ];

			$str = $requestColumn['search']['value'];

			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
				
				
				$columnSearch[] = "".$column['db']." ILIKE ".$binding;
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
		}
		return $where;
	}
static function bind ( &$a, $val, $type )
	{
		//$key = ':binding_'.count( $a );
		$key = "'".$val."'";
		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}


	/**
	 * Pull a particular property from each assoc. array in a numeric array, 
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	static function pluck ( $a, $prop )
	{
		$out = array();
		switch($prop){
			case 'db':
				for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
					if($a[$i][$prop]=='acao'){
						$out[] = $a[$i]['key']." as ".$a[$i][$prop];
					}else if (isset($a[$i]['subquery'])){
						$out[] = $a[$i]['subquery']." as ".$a[$i][$prop];
					}else{	
						$out[] = $a[$i][$prop];
					}
				}				
			break;
			default:
				for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
				//if (isset($a[$i]['subquery'])){
					//	$out[] = $a[$i]['subquery']." as ".$a[$i][$prop];
					//}else{
						$out[] = $a[$i][$prop];
					//}
				}
			break;	
		}
		return $out;
	}


	/**
	 * Return a string from an array or a string
	 *
	 * @param  array|string $a Array to join
	 * @param  string $join Glue for the concatenation
	 * @return string Joined string
	 */
	static function _flatten ( $a, $join = ' AND ' )
	{
		if ( ! $a ) {
			return '';
		}
		else if ( $a && is_array($a) ) {
			return implode( $join, $a );
		}
		return $a;
	}
	
	static function data_output ( $columns, $data )
	{
		$out = array();

		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					switch($column['formatter']){
						case 'img';
							$row[ $column['dt'] ] = "<img src='{$data[$i][ $column['db'] ]}' class='img-circle' width='50px' height='50px' /img>"; //$column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
						break;
						case 'span';
							if(!is_null($data[$i][ $column['db'] ])){
								$row[ $column['dt'] ] = "<button type='button' class='btn btn-warning btn-circle'>{$data[$i][ $column['db'] ]}</button>";
							} else{
								$row[ $column['dt'] ] = $data[$i][ $column['db'] ];
							}
						break;
						
						/*case 'button';
							$editar = "<input type='button' class='btn btn-info btn-sm' value='Editar' onclick=\"control('medicamento/mnt-medicamento?cd_medicamento={$data[$i][ $column['db'] ]}','content-uc')\"></input>"; //$column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
							$excluir = "<input type='button' class='btn btn-warning btn-sm' value='Excluir' onclick=\"excluirMedicamento({$data[$i][ $column['db'] ]})\"></input>"; //$column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
							$row[ $column['dt'] ] = $editar."&nbsp;".$excluir;
						break;*/
						default:
							$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
						break;
					}
					
					
				}
				else {
					$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
				}
			}

			$out[] = $row;
		}

		return $out;
	}

	
	
	
}