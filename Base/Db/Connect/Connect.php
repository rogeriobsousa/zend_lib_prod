<?php
include_once 'Zend/Db/Table/Abstract.php';


class Base_Db_Connect_Connect extends Zend_Db_Table_Abstract  
{    
    protected $_name = 'id';
    public  function __construct($type_database, $host, $port, $username, $password, $dbname)  {
        
    	switch($type_database){
    		case 'postgres':
    			$adaptor = new Zend_Db_Adapter_Pdo_Pgsql(array(
		            'host'     => $host,
		            'username' => $username,
		            'password' => $password,
		            'dbname'   => $dbname,
		            'port'   => $port,
		        ));
		        $this->_db = $adaptor;		
    			parent::__construct();
    			break;
    	}
    	
    	/*$adaptor = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => 'localhost',
            'username' => 'username',
            'password' => 'password',
            'dbname'   => 'database'

        ));
        $this->_db = $adaptor;
        parent::__construct();*/
    }

    // your functions goes here
    public function add($data) {
        // any syntax
    }
}