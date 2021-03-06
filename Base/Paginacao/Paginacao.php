<?php
class Base_Paginacao_Paginacao {
/*Default values*/
		var $total_pages = -1;//items
		var $div = "";//div
		var $limit = null;
		var $target = ""; 
		var $content = ""; 
		var $page = 1;
		var $adjacents = 2;
		var $showCounter = false;
		var $className = "pagination";
		var $parameterName = "page";
		var $urlF = false;//urlFriendly
		var $tipo;
		var $casoDeUso;
		var $direction;
		var $order;
		var $wherePagination;
		var $modulo;
		
		var $comDivRetorno = false;
		var $divRetorno = null;
		var $nomeGrid = null;
		
		/*Buttons next and previous*/
		var $nextT = "Próximo";
		var $nextI = "&#187;"; //&#9658;
//		var $nextI = "<img src='public/img/forward_peq.png'>"; //&#9658;
		var $prevT = "Anterior";
		var $prevI = "&#171;"; //&#9668;

		/*****/
		var $calculate = false;
		
		#Total items
		function items($value){$this->total_pages = (int) $value;}
		function div($div){$this->div = $div;}
		
		
		#how many items to show per page
		function wherePagination($value){$this->wherePagination = $value;}
		function order($value){$this->order = $value;}
		function direction($value){$this->direction = $value;}
		function limit($value){$this->limit = (int) $value;}
		function casoDeUso($value){$this->casoDeUso = $value;}
		function modulo($value){$this->modulo = $value;}
		function tipo($value){$this->tipo = $value;}
		function content($value){$this->content = $value;}
		function comDivRetorno($value){$this->comDivRetorno = $value;}
		function divRetorno($value){$this->divRetorno = $value;}
		function nomeGrid($value){$this->nomeGrid = $value;}
		
		#Page to sent the page value
		function target($value){$this->target = $value;}
		
		#Current page
		function currentPage($value){$this->page = (int) $value;}
		
		#How many adjacent pages should be shown on each side of the current page?
		function adjacents($value){$this->adjacents = (int) $value;}
		
		#show counter?
		function showCounter($value=""){$this->showCounter=($value===true)?true:false;}

		#to change the class name of the pagination div
		function changeClass($value=""){$this->className=$value;}

		function nextLabel($value){$this->nextT = $value;}
		function nextIcon($value){$this->nextI = $value;}
		function prevLabel($value){$this->prevT = $value;}
		function prevIcon($value){$this->prevI = $value;}

		#to change the class name of the pagination div
		function parameterName($value=""){$this->parameterName=$value;}

		#to change urlFriendly
		function urlFriendly($value="%"){
				if(eregi('^ *$',$value)){
						$this->urlF=false;
						return false;
					}
				$this->urlF=$value;
			}
		
		var $pagination;

		function pagination(){}
		function show(){
				if(!$this->calculate)
					if($this->calculate())
						return "<div class=\"$this->className\">$this->pagination</div>\n";
			}
		function get_pagenum_link($id){
				if(strpos($this->target,'?')===false){
						if($this->urlF){
								return str_replace($this->urlF,$id,$this->target);
						}else{
								return "$this->target?$this->parameterName=$id";
						}
				}else{
						return "$this->target&$this->parameterName=$id";
				}
			}
			
			function get_pagenum_link_control($id){
					if($this->urlF){
						return str_replace($this->urlF,$id,$this->target);
					}else{	
						return "$this->target&$this->parameterName=$id";
					}
			}	
		
		function calculate(){
			$this->pagination = "";
				$this->calculate == true;
				$error = false;
				if($this->urlF and $this->urlF != '%' and strpos($this->target,$this->urlF)===false){
						//Es necesario especificar el comodin para sustituir
						echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
						$error = true;
					}elseif($this->urlF and $this->urlF == '%' and strpos($this->target,$this->urlF)===false){
						echo "Es necesario especificar en el target el comodin % para sustituir el número de página<br />";
						$error = true;
					}

				if($this->total_pages < 0){
						echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
						$error = true;
					}
				if($this->limit == null){
						echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
						$error = true;
					}
				if($error)return false;
				
				$n = trim($this->nextT.' '.$this->nextI);
				$p = trim($this->prevI.' '.$this->prevT);
				
				/* Setup vars for query. */
				if($this->page) 
					$start = ($this->page - 1) * $this->limit;             //first item to display on this page
				else
					$start = 0;                                //if no page var is given, set start to 0
			
				/* Setup page vars for display. */
				$prev = $this->page - 1;                            //previous page is page - 1
				$next = $this->page + 1;                            //next page is page + 1
				$lastpage = ceil($this->total_pages/$this->limit);        //lastpage is = total pages / items per page, rounded up.
				
				$_SESSION['qtd_page'.$this->content] = $lastpage;
				$lpm1 = $lastpage - 1;                        //last page minus 1
							
				/* 
					Now we apply our rules and draw the pagination object. 
					We're actually saving the code to a variable in case we want to draw it more than once.
				*/
				if($lastpage > 1){
						if($this->page){
								//anterior button
								if($this->page > 1){
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\" class=\"prev\">$p</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($prev)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\" class=\"prev\">$p</a>";
										else
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($prev)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true&byPagination=true{$this->wherePagination}')\" class=\"prev\">$p</a>";
								}else{
										$this->pagination .= "<span class=\"disabled\">$p</span>";
								}
							}
						//pages
						if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
								for ($counter = 1; $counter <= $lastpage; $counter++){
										if ($counter == $this->page){
												$this->pagination .= "<span class=\"current\">$counter</span>";
										}else{
												//$this->pagination .= "<a href=\"#\" onclick=\"control('control.php','".$this->div."','".$this->get_pagenum_link_control($counter)."','','".$this->tipo."')\">$counter</a>";
											if($this->comDivRetorno)
												$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$counter</a>";
											else
												$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$counter</a>";
										}
									}
							}
						elseif($lastpage > 5 + ($this->adjacents * 2)){//enough pages to hide some
								//close to beginning; only hide later pages
								if($this->page < 1 + ($this->adjacents * 2)){
										for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
												if ($counter == $this->page){
														$this->pagination .= "<span class=\"current\">$counter</span>";
												}else{
														//$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
													if($this->comDivRetorno)
														$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$counter</a>";
													else
														$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$counter</a>";
												}
											}
										$this->pagination .= "...";
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lpm1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$lpm1</a>";
										else
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lpm1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$lpm1</a>";
											//$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lastpage)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$lastpage</a>";
										else	
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lastpage)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$lastpage</a>";
									}
								//in middle; hide some front and some back
								elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)){
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">1</a>";
										else
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">1</a>";
										
											//$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(2)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">2</a>";
										else									
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(2)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">2</a>";
										
										
										$this->pagination .= "...";
										for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
											if ($counter == $this->page){
													$this->pagination .= "<span class=\"current\">$counter</span>";
											}else{
													//$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
												if($this->comDivRetorno)
													$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$counter</a>";
												else
													$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$counter</a>";
											}
										$this->pagination .= "...";
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lpm1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\"\">$lpm1</a>";
										else	
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lpm1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\"\">$lpm1</a>";
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link_control($lastpage)."\">$lastpage</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lastpage)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\"\">$lastpage</a>";
										else	
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($lastpage)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\"\">$lastpage</a>";
									}
								//close to end; only hide early pages
								else{
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">1</a>";
										else	
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(1)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">1</a>";
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
										
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(2)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">2</a>";
										else
											$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control(2)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">2</a>";
										
										$this->pagination .= "...";
										for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
											if ($counter == $this->page){
													$this->pagination .= "<span class=\"current\">$counter</span>";
											}else{
													//$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
												if($this->comDivRetorno)
													$this->pagination .= "<a href=\"#\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$counter</a>";
												else	
													$this->pagination .= "<a href=\"#\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($counter)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$counter</a>";
											}
									}
							}
						if($this->page){
								//siguiente button
								if ($this->page < $counter - 1){
										//$this->pagination .= "<a href=\"".$this->get_pagenum_link($next)."\" class=\"next\">$n</a>";
										if($this->comDivRetorno)
											$this->pagination .= "<a href=\"#\" class=\"next\" onclick=\"gridComDivRetorno('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($next)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}','{$this->divRetorno}','{$this->nomeGrid}')\">$n</a>";
										else	
											$this->pagination .= "<a href=\"#\" class=\"next\" onclick=\"grid('{$this->casoDeUso}',null,'{$this->modulo}','page=".$this->get_pagenum_link_control($next)."&limit=".$this->limit."&direction={$this->direction}&order={$this->order}&byPagination=true{$this->wherePagination}')\">$n</a>";
								}else{
										$this->pagination .= "<span class=\"disabled\">$n</span>";
								}
									if($this->showCounter)$this->pagination .= "<div class=\"pagination_data\">($this->total_pages Pages)</div>";
							}
					}

				return true;
			}
			
			
			/**
			 * Método adicional para returnar a quantidade de páginas
			 * @author: George Henrique R. E. Mendonça
			 * 
			 * @return Int (número de páginas)
			 * @access public
			 *
			 */
			function returnTotalPages() {
				return ceil($this->total_pages / $this->limit);
			}
}
?>