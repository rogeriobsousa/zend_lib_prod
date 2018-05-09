<?php
class Base_Controller_Action extends Zend_Controller_Action
{
	protected $moduleUrl;
	protected $baseUrl;
	protected $page;
	protected $limit;
	protected $order;
	protected $direction;
	
	public function init()
	{
		parent::init();

                
		$namespaceApp = new Zend_Session_Namespace('App'); 

		if(!isset($_SESSION['App']['logado'])){
			//$this->_request->setParam('act','login');
		};
		
		
		$this->view->addHelperPath('Base/View/Helper', 'Base_View_Helper');
		$this->_helper->addPath('Base/Controller/Action/Helper', 'Base_Controller_Action_Helper');
		
		// dados do usuário...
		$userSession = array( "nome"             => "Fulano de Tal da Silva",
                              "dataUltimoAcesso" => "XX/XX/XXXX",
                              "horaUltimoAcesso" => "XX:XX" );
		$this->view->nomeUsuarioLogado = $userSession['nome'];
		$this->view->dataUltimoAcesso  = $userSession['dataUltimoAcesso'];
		$this->view->horaUltimoAcesso  = $userSession['horaUltimoAcesso'];
		
		
		// Recupera a URL raíz do sistema
		$this->baseUrl = $this->_helper->BaseUrl->baseUrl();
		
		// Recupera a URL do módulo
		$this->moduleUrl = $this->_helper->BaseUrl->baseUrl()."/".
						 $this->getRequest()->getModuleName();
                
                
                
	}
	
	public function setPagination($arrDados){
		$this->setPage(isset($arrDados['page']) ? $arrDados['page'] : null);
		$this->setLimit(isset($arrDados['limit']) ? $arrDados['limit'] : null);
		$this->setOrder(isset($arrDados['order']) ? $arrDados['order'] : null);
		$this->setDirection(isset($arrDados['direction']) ? $arrDados['direction'] : null, isset($arrDados['byPagination']) ? $arrDados['byPagination'] : null);
	}
	
	public function setPagination2($arrDados){
		$this->setPage(isset($arrDados['page']) ? $arrDados['page'] : null);
		$this->setLimit(isset($arrDados['limit']) ? $arrDados['limit'] : null);
		$this->setOrder(isset($arrDados['order']) ? $arrDados['order'] : null);
		$this->setDirection(isset($arrDados['direction']) ? $arrDados['direction'] : null, isset($arrDados['byPagination']) ? $arrDados['byPagination'] : null);
	}
	
	public function getPagination(){
		$obj['page'] = $this->getPage();
		$obj['limit'] = $this->getLimit();
		$obj['order'] = $this->getOrder();
		$obj['direction'] = $this->getDirection();
		
		return $obj;
	}
	
	public function setPage($value){
		$this->page = $value ? $value : 1;
	}
	
	public function setLimit($value){
		$this->limit = $value ? $value : 20;
	}
	
	public function setOrder($value){
		$this->order = $value ? $value : null;
	}
	
	public function setDirection($value,$byPagination){
		
		if($byPagination=='true'){
			$this->direction = $value ? $value : 'asc';
		}else{
			$direction = $value ? $value : 'asc';
			$this->direction = ($direction=='desc') ? 'asc' : 'desc';
		}
	}
	
	public function getPage(){
		return $this->page;
	}
	
	public function getLimit(){
		if($this->limit)
		return $this->limit;
		else
		return 20;
	}
	
	public function getOrder(){
		return $this->order;
	}
	
	public function getDirection(){
		return $this->direction;
	}
	
	
	
	public function prepareResult($arrDados,$chave=null){
		$Data = new Base_Controller_Action_Helper_Data();
		//foreach($arrDados as $key=>$value){
		foreach($arrDados['data'] as $key=>$value){
			if($value)
			foreach($value as $kValue=>$vValue){
				if(strpos($kValue,'dt_',0)!==false){
					$arrDados[$key][$kValue]=$Data->converter($vValue,'YYYY-MM-DD','DD/MM/YYYY');				
				}
			}
			
		}
		return $arrDados;
	}
	
	
	public function saveFileImg($array){

		$pasta = $array['pasta'];

		$tipo = $array['tipo'] ? $array['tipo'] : null;


		//$diretorio = getcwd();
		$diretorio = "arquivos";
		
		if(!file_exists($diretorio)){
			mkdir($diretorio,0700);
		}

		if($tipo){
			$diretorio = $diretorio."/".$tipo;
			if(!file_exists($diretorio)){
				mkdir($diretorio,0700);
			}				
		}

//		$diretorio = $diretorio."/fotos";
//		if(!file_exists($diretorio)){
//			mkdir($diretorio,0700);
//		}		

		$diretorio = $diretorio."/".$pasta;
		if(!file_exists($diretorio)){
			mkdir($diretorio,0700);
		}		


		$target_dir = $diretorio.'/';
		$target_file = $target_dir . basename($array["name"]);

		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($pasta)) {
		    $check = getimagesize($array["tmp_name"]);
		    if($check !== false) {
		        //echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        //echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}

		if(isset($array['tx_foto_anterior']))
		if (file_exists($array['tx_foto_anterior'])) {
		    $uploadOk = 0;
		    if (unlink ($array['tx_foto_anterior'])){
		    	$uploadOk = 1;	
		    }
		}
                
		if ($array["size"] > 1000000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "docx") {
		    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($array["tmp_name"],  utf8_decode($target_file))) {
		        //echo "The file ". basename($array["name"]). " has been uploaded.";
		    } else {
		        //echo "Sorry, there was an error uploading your file.";
		    }
		}
		//     echo "<pre>";
		// print_r($target_file);
		// die();
		return $target_file;
	}
	
	public function saveFileKml($array){

		$diretorio = null;
		$target_dir = null;
		$target_file = null;
		$pasta = null;
		$pasta_base = null;
		
		
		
		$pasta_base = $array['pasta_base'];
		$pasta = $array['pasta'];

		$tipo = $array['tipo'] ? $array['tipo'] : null;
		//$diretorio = getcwd();
		$diretorio = "arquivos";
		
		if(!file_exists($diretorio)){
			mkdir($diretorio,0700);
		}

		if($tipo){
			$diretorio = $diretorio."/".$tipo;
			if(!file_exists($diretorio)){
				mkdir($diretorio,0700);
			}				
		}
		
		if($pasta_base){
			$diretorio = $diretorio."/".$pasta_base;
			if(!file_exists($diretorio)){
				mkdir($diretorio,0700);
			}				
		}

		$diretorio = $diretorio."/".$pasta;
		if(!file_exists($diretorio)){
			mkdir($diretorio,0700);
		}			

		$target_dir = $diretorio.'/';
		$target_file = $target_dir . basename($array["name"]);
		
		$uploadOk = 1;
		$kmlFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($pasta)) {
		    $check = filesize($array["tmp_name"]);
		    if($check !== false) {
		        //echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        //echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}

		
		if(isset($array['tx_foto_anterior']))
		if (file_exists($array['tx_foto_anterior'])) {
		    $uploadOk = 0;
		    if (unlink ($array['tx_foto_anterior'])){
		    	$uploadOk = 1;	
		    }
		}
                
		if ($array["size"] > 10000000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}

		
		
		if($kmlFileType != "kml") {
		    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($array["tmp_name"],  utf8_decode($target_file))) {
		        //echo "The file ". basename($array["name"]). " has been uploaded.";
		    } else {
		        //echo "Sorry, there was an error uploading your file.";
		    }
		}
		//     echo "<pre>";
		// print_r($target_file);
		// die();
		return $target_file;
	}
	
	public function saveFileShape($array){

		$diretorio = null;
		$target_dir = null;
		$target_file = null;
		$pasta = null;
		$pasta_base = null;
		
		
		$pasta_base = $array['pasta_base'];

		$tipo = $array['tipo'] ? $array['tipo'] : null;
		//$diretorio = getcwd();
		$diretorio = "arquivos";
		
		if(!file_exists($diretorio)){
			mkdir($diretorio,0700);
		}

		if($tipo){
			$diretorio = $diretorio."/".$tipo;
			if(!file_exists($diretorio)){
				mkdir($diretorio,0700);
			}				
		}
		
		if($pasta_base){
			$diretorio = $diretorio."/".$pasta_base;
			if(!file_exists($diretorio)){
				mkdir($diretorio,0700);
			}				
		}

		$target_dir = $diretorio.'/';
		$target_file = $target_dir . basename($array["name"]);
		
		$uploadOk = 1;
		$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($pasta)) {
		    $check = filesize($array["tmp_name"]);
		    if($check !== false) {
		        //echo "File is an image - " . $check["mime"] . ".";
		        $uploadOk = 1;
		    } else {
		        //echo "File is not an image.";
		        $uploadOk = 0;
		    }
		}
		
		
		if(isset($array['tx_foto_anterior']))
		if (file_exists($array['tx_foto_anterior'])) {
		    $uploadOk = 0;
		    if (unlink ($array['tx_foto_anterior'])){
		    	$uploadOk = 1;	
		    }
		}
                
		if ($array["size"] > 10000000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
		
		if($fileType != "rar") {
		    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($array["tmp_name"],  utf8_decode($target_file))) {
		        //echo "The file ". basename($array["name"]). " has been uploaded.";
		    } else {
		        //echo "Sorry, there was an error uploading your file.";
		    }
		}
		//     echo "<pre>";
		// print_r($target_file);
		// die();
		return $target_file;
	}
	
	// Parses a string into a DateTime object, optionally forced into the given timezone.
	public function parseDateTime($string, $timezone=null) {
		$date = new DateTime(
			$string,
			$timezone ? $timezone : new DateTimeZone('UTC')
				// Used only when the string is ambiguous.
				// Ignored if string has a timezone offset in it.
		);
		if ($timezone) {
			// If our timezone was ignored above, force it.
			$date->setTimezone($timezone);
		}
		return $date;
	}
	
	
	// Takes the year/month/date values of the given DateTime and converts them to a new DateTime,
	// but in UTC.
	public function stripTime($datetime) {
		return new DateTime($datetime->format('Y-m-d'));
	}
	

	public function retornaJsonDataTable($arrayRequest,$obj){
		
		/*
		 * Chamada à metodo criado para tratar o request e configurar os campos do Datatables
		 */
		
		$request = $this->trataRequest($arrayRequest);

		$limit = $request['limit'];
		$page = $request['page'];
		$search = $request['search'];
					
		$arrayConsulta = array();
		if($search){
			$arrayConsulta['busca_geral'] = $search['value'];
		}
		if($request['searchCol']){
			$arrayConsulta['searchCol'] = $request['searchCol'];			
		}
		
		$arrPagination = array('limit'=>$limit,'page'=>$page,'order'=>$request['arrOrder']);

		$objDB = new $obj();
		
		$n = 'get'.$obj;
			
		$arrayConsulta = array_merge($arrayConsulta,$arrayRequest);
		
		$arrDados = $objDB->$n($arrayConsulta,$arrPagination);
		$total = $objDB->getTotal($arrayConsulta);
		$filtrados = $arrDados['total_select'];
		$json_data = array(
		    "draw"            => intval( $arrayRequest['draw'] ),
		    "recordsTotal"    => intval($total),
		    "recordsFiltered" => intval( $filtrados ),
		    "data"            => $arrDados['data'],
		    "page"            => $page,
		    "script"            => $arrDados['script'],
		    "page_send"            => $arrDados['page_send'],
		    "request_params"            => $arrayRequest
		);
		return $json_data;
	}	
	
	public function trataRequest($arrayParametros){
		$arrPar['limit'] = $arrayParametros['length'];
		$arrPar['page'] = ($arrayParametros['start']/$arrPar['limit']) + 1;
		$arrPar['search'] = ($arrayParametros['search']) ?  $arrayParametros['search'] : null;
		$arrPar['columns'] = ($arrayParametros['columns']) ?  $arrayParametros['columns'] : null;
		$arrPar['order'] = ($arrayParametros['order']) ?  $arrayParametros['order'] : null;
				
		$arrOrder = array();
		if($arrPar['order']){
			foreach($arrPar['order'] as $value){
				$coluna = $value['column'];
				$arrOrder[] = $arrPar['columns'][$coluna]['data'].' '.$value['dir'];
			}
		}
		
		$arrPar['arrOrder']=$arrOrder;
		
		
		/* verifica se tem consulta por coluna*/
		/*$arrayConsultaPorColuna = array();
		foreach($arrayParametros['columns'] as $key=>$value){
			if(isset($value['search']['value']) and $value['search']['value']!=''){
				$arrayConsultaPorColuna[$value['data']] = $value['search']['value'];
			}
		}
		*/
		$arrayConsultaPorColuna = null;
		foreach($arrayParametros['columns'] as $key=>$value){
			if(isset($value['search']['value']) and $value['search']['value']!=''){
				if($value['data']!=''){
					$arrayConsultaPorColuna[$value['data']] = $value['search']['value'];
				}else if($value['name']!=''){
					$arrayConsultaPorColuna[$value['name']] = $value['search']['value'];
				}else{
					$arrayConsultaPorColuna = null;
				}
			}
		}
		
		$arrPar['searchCol'] = ($arrayConsultaPorColuna) ? $arrayConsultaPorColuna : null;
		return $arrPar;
	}
	
	
	public function registroLog($arrayLog){
		$L = new LogApp();
		$result = $L->salvar($arrayLog);
	}
	
	public function sanitizeString($str) {
	    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
	    $str = preg_replace('/[éèêë]/ui', 'e', $str);
	    $str = preg_replace('/[íìîï]/ui', 'i', $str);
	    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
	    $str = preg_replace('/[úùûü]/ui', 'u', $str);
	    $str = preg_replace('/[ç]/ui', 'c', $str);
	    // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
	    $str = preg_replace('/[^a-z0-9]/i', '_', $str);
	    $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
	    return $str;
	}
	
	
	
}