<?php

class Base_TreeView_TreeView
{
	protected $base = null;

	protected function real($path) {
		$temp = realpath($path);
		if(!$temp) { throw new Exception('Path does not exist: ' . $path); }
		if($this->base && strlen($this->base)) {
			if(strpos($temp, $this->base) !== 0) { throw new Exception('Path is not inside base ('.$this->base.'): ' . $temp); }
		}
		return $temp;
	}
	protected function path($id) {
		$id = str_replace('/', DIRECTORY_SEPARATOR, $id);
		$id = trim($id, DIRECTORY_SEPARATOR);
		$id = $this->real($this->base . DIRECTORY_SEPARATOR . $id);
		return $id;
	}
	protected function id($path) {
		$path = $this->real($path);
		$path = substr($path, strlen($this->base));
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
		$path = trim($path, '/');
		return strlen($path) ? $path : '/';
	}

	public function __construct() {
		$this->base = '../';
		//if(!$this->base) { throw new Exception('Base directory does not exist'); }
	}
	public function lst($id, $arrLayers) {
		$lst = array();
		$res = array();
		
		$show = false;
		if(isset($_SESSION['dados_acesso']['tp_acesso']) and $_SESSION['dados_acesso']['tp_acesso'] == 'manager'){
			$show=true;
		}
		
		
		if($arrLayers)
		foreach($arrLayers as $key=>$value){
			
			if(isset($value['cd_tipo_simbolizacao']) and ($value['cd_tipo_simbolizacao']=='categorizado' or $value['cd_tipo_simbolizacao']=='graduado')){
				//$button_show = "<input type='checkbox' onclick='showNoShow({$value['cd_layer']})'/>";
				$button_edit = ($show) ? "<a href='javascript:void(0)' onclick='edit_({$value['cd_layer']})'><i class='fa fa-pencil-square-o' id='edit{$value['cd_layer']}'></i></a>" : null;
				$button_slide = "<a href='javascript:void(0)' onclick='showSlide({$value['cd_layer']})'><i class='fa fa-sliders' id='slide{$value['cd_layer']}'></i></a>";
				$button_show = "<a href='javascript:void(0)' onclick='showNoShow({$value['cd_layer']})'><i class='fa fa-eye' id='eye{$value['cd_layer']}'></i></a>";
				$button_delete = "<a href='javascript:void(0)' onclick='removeFromList({$value['cd_layer']})'><i class='glyphicon glyphicon-trash'></i></a>";
				$button_extent = "<a href='javascript:void(0)' onclick='fitToLayer({$value['cd_layer']})'><i class='fa fa-compress'></i></a>";
				//$text = $value['tx_nome_tabela']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$text = "<div style='float:left; width:230px;'> &nbsp;{$value['tx_nome_tabela']}</div><div style='float:right'>&nbsp;$button_edit&nbsp;$button_slide&nbsp;$button_show&nbsp;$button_extent&nbsp;$button_delete&nbsp;</div>";
				$res[] = array('text' => $text, 'children' => true, 'type' => 'default', 'id' => $value['cd_layer'], 'icon' => 'fa');
			}else{
				if(isset($value['tx_nome_tabela'])){
					$button_edit = ($show) ? "<a href='javascript:void(0)' onclick='edit_({$value['cd_layer']})'><i class='fa fa-pencil-square-o' id='edit{$value['cd_layer']}'></i></a>" : null;
					//$button_edit = "<a href='javascript:void(0)' onclick='edit_({$value['cd_layer']})'><i class='fa fa-pencil-square-o' id='edit{$value['cd_layer']}'></i></a>";
					$button_slide = "<a href='javascript:void(0)' onclick='showSlide({$value['cd_layer']})'><i class='fa fa-sliders' id='slide{$value['cd_layer']}'></i></a>";
					$button_show = "<a href='javascript:void(0)' onclick='showNoShow({$value['cd_layer']})'><i class='fa fa-eye' id='eye{$value['cd_layer']}'></i></a>";
					$button_delete = "<a href='javascript:void(0)' onclick='removeFromList({$value['cd_layer']})'><i class='glyphicon glyphicon-trash'></i></a>";
					$button_extent = "<a href='javascript:void(0)' onclick='fitToLayer({$value['cd_layer']})'><i class='fa fa-compress'></i></a>";
					$text = "<div style='float:left; width:230px;'>&nbsp;{$value['tx_nome_tabela']}</div><div style='float:right'>&nbsp;$button_edit&nbsp;$button_slide&nbsp;$button_show&nbsp;$button_extent&nbsp;$button_delete&nbsp;</div>";
					$res[] = array('text' => $text, 'children' => false, 'type' => 'default', 'id' => $value['cd_layer'], 'icon' => 'fa');
				}else{
					$exp = explode('|',$value);
					$text = "<div class='' style='float:left;'>{$exp[0]}</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style='margin-top:3px; margin-left:20px; float:left; background-color: {$exp[1]}; height: 10px; border: 3px {$exp[2]} solid'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";
					$res[] = array('text' => $text, 'children' => false, 'id' => $exp[0], 'type' => 'file', 'icon' => 'fa');
				}
			}
		}
		return $res;
	}
	public function data($id) {
		if(strpos($id, ":")) {
			$id = array_map(array($this, 'id'), explode(':', $id));
			return array('type'=>'multiple', 'content'=> 'Multiple selected: ' . implode(' ', $id));
		}
		$dir = $this->path($id);
		if(is_dir($dir)) {
			return array('type'=>'folder', 'content'=> $id);
		}
		if(is_file($dir)) {
			$ext = strpos($dir, '.') !== FALSE ? substr($dir, strrpos($dir, '.') + 1) : '';
			$dat = array('type' => $ext, 'content' => '');
			switch($ext) {
				case 'txt':
				case 'text':
				case 'md':
				case 'js':
				case 'json':
				case 'css':
				case 'html':
				case 'htm':
				case 'xml':
				case 'c':
				case 'cpp':
				case 'h':
				case 'sql':
				case 'log':
				case 'py':
				case 'rb':
				case 'htaccess':
				case 'php':
					$dat['content'] = file_get_contents($dir);
					break;
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'png':
				case 'bmp':
					$dat['content'] = 'data:'.finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir).';base64,'.base64_encode(file_get_contents($dir));
					break;
				default:
					$dat['content'] = 'File not recognized: '.$this->id($dir);
					break;
			}
			return $dat;
		}
		throw new Exception('Not a valid selection: ' . $dir);
	}
	public function create($id, $name, $mkdir = false) {
		$dir = $this->path($id);
		if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
			throw new Exception('Invalid name: ' . $name);
		}
		if($mkdir) {
			mkdir($dir . DIRECTORY_SEPARATOR . $name);
		}
		else {
			file_put_contents($dir . DIRECTORY_SEPARATOR . $name, '');
		}
		return array('id' => $this->id($dir . DIRECTORY_SEPARATOR . $name));
	}
	public function rename($id, $name) {
		$dir = $this->path($id);
		if($dir === $this->base) {
			throw new Exception('Cannot rename root');
		}
		if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
			throw new Exception('Invalid name: ' . $name);
		}
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		array_pop($new);
		array_push($new, $name);
		$new = implode(DIRECTORY_SEPARATOR, $new);
		if($dir !== $new) {
			if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }
			rename($dir, $new);
		}
		return array('id' => $this->id($new));
	}
	public function remove($id) {
		$dir = $this->path($id);
		if($dir === $this->base) {
			throw new Exception('Cannot remove root');
		}
		if(is_dir($dir)) {
			foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
				$this->remove($this->id($dir . DIRECTORY_SEPARATOR . $f));
			}
			rmdir($dir);
		}
		if(is_file($dir)) {
			unlink($dir);
		}
		return array('status' => 'OK');
	}
	public function move($id, $par) {
		
		$dir = $this->path($id);
		$par = $this->path($par);
		echo "<pre>";
		print_r($dir);
		echo "<BR>";
		die(__FILE__."-".__LINE__);
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		$new = array_pop($new);
		$new = $par . DIRECTORY_SEPARATOR . $new;
		rename($dir, $new);
		return array('id' => $this->id($new));
	}
	public function copy($id, $par) {
		$dir = $this->path($id);
		$par = $this->path($par);
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		$new = array_pop($new);
		$new = $par . DIRECTORY_SEPARATOR . $new;
		if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }

		if(is_dir($dir)) {
			mkdir($new);
			foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
				$this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
			}
		}
		if(is_file($dir)) {
			copy($dir, $new);
		}
		return array('id' => $this->id($new));
	}
}
/*
if(isset($_GET['operation'])) {
	$fs = new fs(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR);
	try {
		$rslt = null;
		switch($_GET['operation']) {
			case 'get_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->lst($node, (isset($_GET['id']) && $_GET['id'] === '#'));
				break;
			case "get_content":
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->data($node);
				break;
			case 'create_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->create($node, isset($_GET['text']) ? $_GET['text'] : '', (!isset($_GET['type']) || $_GET['type'] !== 'file'));
				break;
			case 'rename_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->rename($node, isset($_GET['text']) ? $_GET['text'] : '');
				break;
			case 'delete_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->remove($node);
				break;
			case 'move_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
				$rslt = $fs->move($node, $parn);
				break;
			case 'copy_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
				$rslt = $fs->copy($node, $parn);
				break;
			default:
				throw new Exception('Unsupported operation: ' . $_GET['operation']);
				break;
		}
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($rslt);
	}
	catch (Exception $e) {
		header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
		header('Status:  500 Server Error');
		echo $e->getMessage();
	}
	die();
}*/