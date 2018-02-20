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
		foreach($arrDados as $key=>$value){
			if($value)
			foreach($value as $kValue=>$vValue){
				if(strpos($kValue,'dt_',0)!==false){
					$arrDados[$key][$kValue]=$Data->converter($vValue,'YYYY-MM-DD','DD/MM/YYYY');				
				}
			}
			
		}
		return $arrDados;
	}
	
	
	public function saveFile($array){

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

		if (file_exists($array['tx_foto_anterior'])) {
		    $uploadOk = 0;
		    if (unlink ($array['tx_foto_anterior'])){
		    	$uploadOk = 1;	
		    }
		}
                
		if ($array["size"] > 500000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
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
	
	
	
}