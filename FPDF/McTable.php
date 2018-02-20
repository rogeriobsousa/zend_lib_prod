<?php
include_once 'PDF_PageGroup.php';
class McTable extends PDF_PageGroup
{
	var $widths;
	var $aligns;
	var $bold;
	var $border = true;
	var $sizeFont;
	var $typeFont;
	var $SpaceLine = 5;
	var $Space = 1;
	var $height = 10; // Adcionado o $height por Jo�o Paulo
	
	// Adcionado o SetHeight por Jo�o Paulo
	function SetHeight($h)
	{
		//Set the array of height rows
		$this->height=$h;
	}	

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}
	function SetBold($b)
	{
		//Set the array caso deseja negrito.
		$this->bold=$b;
	}

	function SetBorder($bo)
	{
		//Set the array caso deseja negrito.
		$this->border=$bo;
	}

	function SetSizeFont($s)
	{
		//Set the array com a font.
		$this->sizeFont=$s;
	}

	function SetTypeFont($t)
	{
		//Set the array com a font.
		$this->typeFont=$t;
	}

	function SetSpaceLine($l)
	{
		//Set the array com a font.
		$this->SpaceLine=$l;
	}

	function SetSpace($S=0)
	{
		//Set the array com a font.
		$this->Space=$S;
	}

	function Row($data,$fill=0)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=$this->SpaceLine*$nb+$this->Space;
		
		
		//Issue a page break first if needed
		//$this->CheckPageBreak($h);
		
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
		
			if ($this->border == true){
				//Draw the border
				$this->Rect($x,$y,$w,$h);
			}
			
			//Formata a fonte
			//$this->SetFont('Arial',$this->bold[$i],12);
			$this->SetFont($this->typeFont,$this->bold[$i],$this->sizeFont);
			//die("para ==> ".$this->font);
			//$this->SetFont("'".$this->fontF."'",$this->bold[$i],12);
			//Print the text
            if( $fill === 1 ){
                $this->MultiCell($w,$this->SpaceLine,$data[$i],0,$a,1);
            } else {
                $this->MultiCell($w,$this->SpaceLine,$data[$i],0,$a,$fill); // Adcionado o $fill por Jo�o Paulo
            }
            //Put the position to the right of the cell
			//$this->SetXY($x+$w,$y);
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
		
		$this->CheckPageBreak($h);

		
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
}
?>