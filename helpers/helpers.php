<?php
	require_once('templates/fpdf182/fpdf.php'); 

	function media()
	{
		return SERVERURL . '/assets';
	}

	function dep($data)
	{
		$format = print_r('<pre>');
		$format .= print_r($data);
		$format .= print_r('</pre>');
		return $format;
	}

	function getModal(string $nameModal, $data)
	{
		$view_modal = "views/templates/modals/{$nameModal}.php";
		require_once($view_modal);
	}

	function getImage($dir, $cDirectorio){
		$dir = $_SERVER['DOCUMENT_ROOT'].'/Nomina'. $dir;
		if(!file_exists($cDirectorio)){
			$cDirectorio = ucwords(strtolower($cDirectorio));
		}

		if(strrpos($dir, "/HV/") === false){
			$dir = $dir . '/HV/';
		}

		if(strrpos($cDirectorio, "/HV/") === false){
			$cDirectorio = $cDirectorio . '/HV/';
		}

		if (file_exists($dir)){
			$path = '';
			$files = scandir($dir);
			for($i = 0; $i < count($files); $i ++){
				if(strrpos($files[$i], "_FOTOGRAFIA.") !== FALSE ||
				strrpos($files[$i], "_Foto.") !== FALSE){
					$path_parts = pathinfo($cDirectorio . $files[$i]);
					if($path_parts["extension"] == "JPG" || $path_parts["extension"] == "jpg"
						|| $path_parts["extension"] == "JPEG" || $path_parts["extension"] == "jpeg"
						|| $path_parts["extension"] == "png"){
						$path = $cDirectorio . $files[$i];
					}
				}	
			}
			if($path !== ''){
				return $path;
			}else{
				return  SERVERURL . '/assets/images/avatar.png';	
			}
		}else{
			return  SERVERURL . '/assets/images/avatar.png';
		}
	}


	function strClean($string)
	{
    	$string = strip_tags($string); // Eliminar etiquetas HTML
    	$string = htmlspecialchars($string); // Escapar caracteres especiales

		$string = trim($string);
		$string = str_replace('  ', ' ', $string);
		$string = stripslashes($string);
		$string = str_ireplace("SELECT * FROM", "", $string);
		$string = str_ireplace('SELECT COUNT(*) FROM', '', $string);
		$string = str_ireplace("DELETE FROM", "", $string);
		$string = str_ireplace("INSERT INTO", "", $string);
		$string = str_ireplace("DROP TABLE", "", $string);
		$string = str_ireplace("DROP DATABASE", "", $string);
		$string = str_ireplace("TRUNCATE TABLE", "", $string);
		$string = str_ireplace("SHOW TABLES", "", $string);
		$string = str_ireplace("SHOW DATABASES", "", $string);
		$string = str_ireplace("<?php", "", $string);
		$string = str_ireplace("?>", "", $string);
		$string = str_ireplace("--", "", $string);
		$string = str_ireplace(">", "", $string);
		$string = str_ireplace("<", "", $string);
		$string = str_ireplace("[", "", $string);
		$string = str_ireplace("]", "", $string);
		$string = str_ireplace("^", "", $string);
		$string = str_ireplace("==", "", $string);
		$string = str_ireplace(";", "", $string);
		$string = str_ireplace("::", "", $string);
		return $string;
	}

	function passGenerator($long = 10)
	{
		$pass = '';
		$cadena = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxya1234567890';
		$longCadena = strlen($cadena);

		for ($i = 0; $i < $long; $i++) 
		{ 
			$pos = rand(0, $longCadena -1);
			$pass .= substr($cadena, $pos, 1);
		}

		return $pass;
	}

	function token()
	{
		$r1 = bin2hex(random_bytes(10));
		$r2 = bin2hex(random_bytes(10));
		$r3 = bin2hex(random_bytes(10));
		$r4 = bin2hex(random_bytes(10));
		$token = $r1 . '-' . $r2 . '-' . $r3 . '-' . $r4;
		return $token;
	}

	function formatMoney($valor)
	{
		$valor = SMONEY . number_format($valor, 2, SPD, SPM);
		return $valor;
	}

	function NombreDia($tnDia)
	{
		switch ($tnDia) 
		{
			case 0:
			  $lcDia = 'Domingo';
			  break;
			case 1:
			  $lcDia = 'Lunes';
			  break;
			case 2:
			  $lcDia = 'Martes';
			  break;
			case 3:
			  $lcDia = 'Miércoles';
			  break;
			case 4:
			  $lcDia = 'Jueves';
			  break;
			case 5:
			  $lcDia = 'Viernes';
			  break;
			case 6:
			  $lcDia = 'Sábado';
			  break;
		}
		
		return $lcDia;
		
	}
	
	function NombreMes($tnMes)
	{
		switch ($tnMes) 
		{
			case 1:
				$lcMes = 'Enero';
				break;
			case 2:
				$lcMes = 'Febrero';
				break;
			case 3:
				$lcMes = 'Marzo';
				break;
			case 4:
				$lcMes = 'Abril';
				break;
			case 5:
				$lcMes = 'Mayo';
				break;
			case 6:
				$lcMes = 'Junio';
				break;
			case 7:
				$lcMes = 'Julio';
				break;
			case 8:
				$lcMes = 'Agosto';
				break;
			case 9:
				$lcMes = 'Septiembre';
				break;
			case 10:
				$lcMes = 'Octubre';
				break;
			case 11:
				$lcMes = 'Noviembre';
				break;
			case 12:
				$lcMes = 'Diciembre';
				break;
			default:
				$lcMes = '';
				break;
		}
		
		return $lcMes;
		
	}

	function script_fecha($tdFecha = '')
	{
		if (empty($tdFecha))
			$tdFecha = date('Y-m-d');

		$lcFecha = NombreDia(date('w', strtotime($tdFecha)));
    
		$lcFecha .= ' ' . date('d', strtotime($tdFecha)) . ' de ' . NombreMes(date('m', strtotime($tdFecha)));
    
		$lcFecha .= ' de ' . date('Y', strtotime($tdFecha));
    
		return( $lcFecha ) ;
	}

	function label($tcLabel)
	{
		if	( isset($_SESSION['Login']['IdIdioma']) AND $_SESSION['Login']['IdIdioma'] > 0 )
		{
			$lnConn = ConnDB();

			$lcQT = 'SELECT TRADUCCIONES.* ' .
						'FROM TRADUCCIONES ' .
						'WHERE ' .
						"TRADUCCIONES.Texto = '" . $tcLabel . "' AND " .
						'TRADUCCIONES.IdIdioma = ' . $_SESSION['Login']['IdIdioma'];
						
			$loQT = pg_query($lnConn, $lcQT);
			
			if	( pg_num_rows($loQT) > 0 )
			{
				$loRT = pg_fetch_object($loQT);
				
				if	( ! empty($loRT->traduccion) )
					$lcTraduccion = $loRT->traduccion;
				else
					$lcTraduccion = '[' . $tcLabel . ']';
			}
			else
			{
				$lcQT = 'INSERT INTO TRADUCCIONES ' .
						'( Texto, IdIdioma ) ' .
						'VALUES ( ' .
						"'" . $tcLabel . "', " .
						$_SESSION['Login']['IdIdioma'] . ') ';
						
				pg_query($lnConn, $lcQT);
				
				$lcTraduccion = '[' . $tcLabel . ']';
			}
		}
		else
			$lcTraduccion = $tcLabel;
		
		return trim($lcTraduccion);
	}

	function paginar()
	{
		$lcLink = func_get_arg(0);
		$tnNumRegistros = func_get_arg(1);
		$tnPagina = func_get_arg(2);

		if (func_num_args() == 4)
			$tnParametroAdicional = func_get_arg(3);
		else
			$tnParametroAdicional = false;

		$lnNumPaginas = ceil($tnNumRegistros / LINES);
		$lcPagesLink = "";

		if	($lnNumPaginas > 1)
		{
			$lcPagesLink = '<ul class="pagination">';
			
			if	($tnPagina > 1)
			{
				$lnPaginaAnt = $tnPagina - 1;

				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnPaginaAnt . '/' . $tnParametroAdicional . '"><i class="material-icons">chevron_left</i></a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnPaginaAnt . '"><i class="material-icons">chevron_left</i></a></li>';
			}
			else
			{
				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/1/ ' . $tnParametroAdicional . '"><i class="material-icons">chevron_left</i></a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/1"><i class="material-icons">chevron_left</i></a></li>';
			}
			
			$lnRango = 5;
			$lnRangoMin = max(1, $tnPagina - (($lnRango - 1) / 2));
			$lnRangoMax = min($lnNumPaginas, $tnPagina + (($lnRango - 1) / 2));
			
			if	(($lnRangoMax - $lnRangoMin) < ($lnRango - 1))
			{
				if	($lnRangoMin == 1)
					$lnRangoMax = min($lnRangoMin + ($lnRango - 1), $lnNumPaginas);
				else
					$lnRangoMin = max($lnRangoMax - ($lnRango - 1), 0);
			}
			
			if	($lnRangoMin > 1)
			{
				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/1/' . $tnParametroAdicional . '">1</a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/1">1</a></li>';
				$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href=""">...</a></li>';
			}				
			
			for	($lnCount = 1; $lnCount <= $lnNumPaginas; $lnCount++)
			{
				if	($lnCount == $tnPagina)
					if ($tnParametroAdicional !== false)
						$lcPagesLink .= '<li class="active"><a class="white-text cyan darken-4" href="' . $lcLink . '/' . $lnCount . '/' . $tnParametroAdicional . '">' . $lnCount . '</a></li>';
					else
						$lcPagesLink .= '<li class="active"><a class="white-text cyan darken-4" href="' . $lcLink . '/' . $lnCount . '">' . $lnCount . '</a></	li>';
					else
				{
					if	($lnRangoMin <= $lnCount and $lnCount <= $lnRangoMax)
						if ($tnParametroAdicional !== false)
							$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnCount . '/' . $tnParametroAdicional . '">' . $lnCount . '</a></li>';
						else
							$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnCount . '">' . $lnCount . '</a></li>';
					}
			}
			
			if	($lnRangoMax < $lnNumPaginas)
			{
				$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href=""">...</a></li>';
				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnNumPaginas . '/' . $tnParametroAdicional . '">' . $lnNumPaginas . '</a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnNumPaginas . '">' . $lnNumPaginas . '</a></li>';
			}
			
			if	(($tnNumRegistros - (LINES * $tnPagina)) > 0)
			{
				$lnPaginaSig = $tnPagina + 1;
				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnPaginaSig . '/' . $tnParametroAdicional . '"><i class="material-icons">chevron_right</i></a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnPaginaSig . '"><i class="material-icons">chevron_right</i></a></li>';
			}
			else
				if ($tnParametroAdicional !== false)
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnNumPaginas . '/' . $tnParametroAdicional . '"><i class="material-icons">chevron_right</i></a></li>';
				else
					$lcPagesLink .= '<li class="waves-effect"><a class="white-text" href="' . $lcLink . '/' . $lnNumPaginas . '"><i class="material-icons">chevron_right</i></a></li>';

			$lcPagesLink .= "</ul>";
		}
		
		return $lcPagesLink;
		
	}

	function eliminarAcentos($cadena)
	{
		$cadena = str_replace(
			array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
			array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
			$cadena);

		$cadena = str_replace(
			array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
			array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
			$cadena);

		$cadena = str_replace(
			array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
			array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
			$cadena);

		$cadena = str_replace(
			array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
			array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
			$cadena);

		$cadena = str_replace(
			array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
			array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
			$cadena);

		$cadena = str_replace(
			array('Ñ', 'ñ', 'Ç', 'ç'),
			array('N', 'n', 'C', 'c'),
			$cadena);
		
		return $cadena;
	}

	class PDF extends FPDF 
	{ 
		function Header() 
		{ 
			global $lcOrientacion;
			global $lcTitulo;
			global $lcSubTitulo;
			global $lcEncabezado;
			global $lcEncabezado2;

			if	( $lcOrientacion == 'L' )
				$this->SetAutoPageBreak(TRUE, 15);
			else
				$this->SetAutoPageBreak(TRUE, 15);

			$this->SetTitle($lcTitulo);
			$this->SetFont('Arial', '', 9); 
			$this->SetLineWidth(0.2);

			$this->Image(LOGOTIPO, 15, 17, 50, 10); 

			$this->SetY(20);
			$this->SetFont('Arial', '', 7); 
			$this->Cell(80, 9, 'Nit. ' . $_SESSION['Empresa']['Nit'], 0, 0, 'R'); 
			$this->SetFont('Arial', '', 14); 

			$this->SetY(30);
			if	( $lcOrientacion == 'L' )
            	$this->Line($this->GetX(), $this->GetY(), 285, $this->GetY()); 
			else
            	$this->Line($this->GetX(), $this->GetY(), 200, $this->GetY()); 
			// $this->SetY(30);
			// $this->Ln(); 
			$this->Cell(00, 9, $lcTitulo, 0, 0, 'C', FALSE);
			$this->Ln(); 
			if	( ! empty($lcSubTitulo) )
			{
				$this->SetFont('Arial', '', 12); 
				$this->Cell(0, 9, $lcSubTitulo, 0, 0, 'C', FALSE);
				$this->Ln(); 
			}

			if	( $lcOrientacion == 'L' )
            	$this->Line($this->GetX(), $this->GetY(), 285, $this->GetY()); 
			else
            	$this->Line($this->GetX(), $this->GetY(), 200, $this->GetY()); 

			$this->Ln(2); 
			
			$this->SetFont('Arial', '', 7); 

			if	( ! empty($lcEncabezado) )
			{
				$this->SetFillColor(192, 192, 192);
				$this->Cell(0, 5, '', 0, 0, 'L', TRUE);
				$this->Ln(); 
				$this->Cell(0, 5, $lcEncabezado, 0, 0, 'L', TRUE);
				$this->SetFillColor(255, 255, 255);
				$this->Ln(); 
			}
			if	( ! empty($lcEncabezado2) )
			{
				$this->SetFillColor(192, 192, 192);
				$this->Cell(0, 5, $lcEncabezado2, 0, 0, 'L', TRUE);
				$this->SetFillColor(255, 255, 255);
				$this->Ln(); 
			}

			$this->SetFont('Arial', '', 7); 
		} 

        function Footer() 
		{ 
			global $lcOrientacion;

			$this->setY(-15);
	
			if	( $lcOrientacion == 'L' )
            	$this->Line($this->GetX(), $this->GetY(), 285, $this->GetY()); 
			else
            	$this->Line($this->GetX(), $this->GetY(), 200, $this->GetY()); 
			$this->SetFont('Arial', '', 7); 
			// $this->Cell(00, 10, utf8_decode($_SESSION['Empresa']['Direccion']), 0, 0, 'C'); 
			// $this->Ln(5); 
			// $this->Cell(00, 10, utf8_decode($_SESSION['Empresa']['Telefono'] . ' - ' . $_SESSION['Empresa']['Email']), 0, 0, 'C'); 
			// $this->Ln(); 
			// if	( $lcOrientacion == 'L' )
            // 	$this->Line($this->GetX(), $this->GetY(), 285, $this->GetY()); 
			// else
            // 	$this->Line($this->GetX(), $this->GetY(), 200, $this->GetY()); 
			$this->Cell(60, 10, utf8_decode('Fecha impresión: ') . date('Y-m-d') . ' [' . $_SESSION['Login']['Usuario'] . ']', 0, 0, 'L'); 
			$this->Cell(00, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R'); 
			$this->Ln(5); 
			$this->SetFont('Arial', '', 9);
		} 

	}

	function hex2dec($couleur = "#000000"){
		$R = substr($couleur, 1, 2);
		$rouge = hexdec($R);
		$V = substr($couleur, 3, 2);
		$vert = hexdec($V);
		$B = substr($couleur, 5, 2);
		$bleu = hexdec($B);
		$tbl_couleur = array();
		$tbl_couleur['R']=$rouge;
		$tbl_couleur['G']=$vert;
		$tbl_couleur['B']=$bleu;
		return $tbl_couleur;
	}
	
	//conversion pixel -> millimeter in 72 dpi
	function px2mm($px){
		return $px*25.4/72;
	}
	
	function txtentities($html){
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		return strtr($html, $trans);
	}

	class PDF2 extends FPDF 
	{ 
		protected $B = 0;
		protected $I = 0;
		protected $U = 0;
		protected $HREF = '';
		
		function WriteHTML($html)
		{
			// Intérprete de HTML
			$html = str_replace("\n",' ',$html);
			$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
			foreach($a as $i=>$e)
			{
				if($i%2==0)
				{
					// Text
					if($this->HREF)
						$this->PutLink($this->HREF,$e);
					else
						$this->MultiCell(170, 5, $e, 0, 'J', FALSE);						
					// $this->Write(5,$e);

				}
				else
				{
					// Etiqueta
					if($e[0]=='/')
						$this->CloseTag(strtoupper(substr($e,1)));
					else
					{
						// Extraer atributos
						$a2 = explode(' ',$e);
						$tag = strtoupper(array_shift($a2));
						$attr = array();
						foreach($a2 as $v)
						{
							if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
								$attr[strtoupper($a3[1])] = $a3[2];
						}
						$this->OpenTag($tag,$attr);
					}
				}
			}
		}
		
		function OpenTag($tag, $attr)
		{
			// Etiqueta de apertura
			if($tag=='B' || $tag=='I' || $tag=='U')
				$this->SetStyle($tag,true);
			if($tag=='A')
				$this->HREF = $attr['HREF'];
			if($tag=='BR')
				$this->Ln(5);
		}
		
		function CloseTag($tag)
		{
			// Etiqueta de cierre
			if($tag=='B' || $tag=='I' || $tag=='U')
				$this->SetStyle($tag,false);
			if($tag=='A')
				$this->HREF = '';
		}
		
		function SetStyle($tag, $enable)
		{
			// Modificar estilo y escoger la fuente correspondiente
			$this->$tag += ($enable ? 1 : -1);
			$style = '';
			foreach(array('B', 'I', 'U') as $s)
			{
				if($this->$s>0)
					$style .= $s;
			}
			$this->SetFont('',$style);
		}
		
		function PutLink($URL, $txt)
		{
			// Escribir un hiper-enlace
			$this->SetTextColor(0,0,255);
			$this->SetStyle('U',true);
			$this->Write(5,$txt,$URL);
			$this->SetStyle('U',false);
			$this->SetTextColor(0);
		}
				function Header() 
		{ 
			global $lcOrientacion;
			global $Asunto;
			global $CodigoDocumento;

			$this->SetTitle($Asunto);
			$this->SetFont('Arial', '', 9); 
			$this->SetLineWidth(0.2);

			$this->Image(LOGOTIPO, 20, 17, 50, 10); 

			$this->SetY(40);
           	$this->Line($this->GetX(), $this->GetY(), 195, $this->GetY()); 

			if ($this->PageNo() == 1) 
			{
				$this->SetFont('Arial', '', 14); 
				$this->Ln(); 
				$this->Cell(00, 10, $Asunto, 0, 0, 'C', FALSE);
				$this->Ln(); 
				$this->Line($this->GetX(), $this->GetY(), 195, $this->GetY()); 
				$this->Ln(); 
			}
		} 

        function Footer() 
		{ 
			global $lcOrientacion;
			global $CodigoDocumento;

			$this->setY(-20);

			$this->Line($this->GetX(), $this->GetY(), 195, $this->GetY()); 
			$this->Cell(00, 20, $CodigoDocumento, 0, 0, 'L'); 
			$this->Cell(00, 20, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R'); 
		} 
	}

	function get($tcLabel, $tcNombre, $tcValue, $tcTipo, $tnTamano, $tlRO, $tcAyuda, $tcIcon, $tcAutocomplete = FALSE)
	{
		if	( $tcTipo == 'file' )
			echo '<div class="file-field input-field">';
		else
			echo '<div class="input-field">';
	
		switch ( $tcTipo )
		{
			case 'file':
				if	( $tlRO )
				{
					echo '<div class="btn teal lighten-3">';
						echo '<span>Archivo</span>';
						echo '<input type="file" accept="' . $tcValue . '" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' readonly>';
					echo '</div>';
					echo '<div class="file-path-wrapper">';
						echo '<input class="file-path validate" type="text">';
					echo '</div>';
				}
				else
				{
					echo '<div class="btn teal lighten-3">';
						echo '<span>Archivo</span>';
						if	( $tcAyuda == 'multiple' )
							echo '<input type="file" accept="' . $tcValue . '" id="' . $tcNombre . '[]" name="' . $tcNombre . '[]" ' . $tcAyuda . '>';
						else
							echo '<input type="file" accept="' . $tcValue . '" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . '>';
					echo '</div>';
					echo '<div class="file-path-wrapper">';
						if	( $tcAyuda == 'multiple' )
							echo '<input class="file-path validate" type="text" placeholder="' . $tcLabel . '">';
						else
							echo '<input class="file-path validate" type="text" placeholder="' . $tcLabel . '">';
					echo '</div>';
				}
				break;
				
			case 'datalist':
				// if	( ! empty($tcIcon) )
				// 	echo '<i class="' . $tcIcon . ' prefix"></i>';

				if	( $tlRO )
				{
					echo '<input list="dl' . $tcNombre . '" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" readonly>';
					echo '<datalist id="dl' . $tcNombre . '">';
						// echo '<option value=0>Seleccione un(a) ' . $tcLabel . '</option>';
						echo $tcValue;
					echo '</datalist>';
					echo '<label>' . $tcLabel . '</label>';
				}
				else
				{
					echo '<input list="dl' . $tcNombre . '" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" autofocus>';
					echo '<datalist id="dl' . $tcNombre . '">';
						// echo '<option value=0>Seleccione un(a) ' . $tcLabel . '</option>';
						// echo '<option value=0>Seleccione una opción</option>';
						echo $tcValue;
					echo '</datalist>';
					echo '<label>' . $tcLabel . '</label>';
				}
				
				break;
			
			case 'select':
			case 'select_col':
				if	( $tlRO )
				{
					echo '<select id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' disabled>';
						// echo '<option value=0>Seleccione un(a) ' . $tcLabel . '</option>';
						echo $tcValue;
					echo '</select>';
					echo '<label>' . $tcLabel . '</label>';
				}
				else
				{
					echo '<select id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' autofocus="autofocus">';
						echo '<option value=0>Seleccione un(a) ' . $tcLabel . '</option>';
						// echo '<option value=0>Seleccione una opción</option>';
						echo $tcValue;
					echo '</select>';
					echo '<label>' . $tcLabel . '</label>';
				}
				
				break;
					
			case 'select multiple':
				// if	( ! empty($tcIcon) )
				// 	echo '<i class="' . $tcIcon . ' prefix"></i>';

				if	( $tlRO )
				{
					echo '<select multiple="multiple" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' disabled>';
						// echo '<option value=>Seleccione un(a) ' . $tcLabel . '</option>';
						echo $tcValue;
					echo '</select>';
					echo '<label>' . $tcLabel . '</label>';
				}
				else
				{
					echo '<select multiple="multiple" id="' . $tcNombre . '[]" name="' . $tcNombre . '[]" ' . $tcAyuda . ' autofocus="autofocus">';
						// echo '<option value=>Seleccione ' . $tcLabel . '</option>';
						echo '<option value=0>Seleccione una o más opciones</option>';
						echo $tcValue;
					echo '</select>';
					echo '<label>' . $tcLabel . '</label>';
				}
				
				break;
				
			case 'textarea':
			case 'textarea_col':
				echo '<i class="material-icons prefix">short_text</i>';

				if	( $tlRO )
					echo '<textarea ' . $tcAyuda . ' class="materialize-textarea" rows="' . $tnTamano . '" ' . 
						'id="' . $tcNombre . '" name="' . $tcNombre . '" readonly>';
				else
					echo '<textarea ' . $tcAyuda . ' class="materialize-textarea" rows="' . $tnTamano . '" ' . 
						'id="' . $tcNombre . '" name="' . $tcNombre . '">';
					echo $tcValue;
				echo '</textarea>';

				break;
				
			case 'checkbox':
				if	( $tlRO )
				{
					if	( $tnTamano >= 1 )
					{
						if	( ! empty($tcValue) )
							echo '<p><label>' . 
								'<input type="checkbox" class=" orange darken-3" id="' . $tcNombre . '" name="' . $tcNombre . '" ' .
								'value="' . $tcValue . '" ' . $tcAyuda . ' disabled checked="checked"/>' .
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
						else
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' readonly checked="checked"/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
					}
					else
					{
						if	( ! empty($tcValue) )
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' .
								'value="' . $tcValue . '" ' . $tcAyuda . ' disabled/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
						else
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' .
								'value="' . $tcValue . '" ' . $tcAyuda . ' disabled/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
					}
				}
				else
				{
					if	( $tnTamano == 1 )
					{
						if	( ! empty($tcValue) )
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' .
								'value="' . $tcValue . '" ' . $tcAyuda . ' checked="checked"/>' .
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
						else
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' checked="checked"/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
					}
					else
					{
						if	( ! empty($tcValue) )
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' .
								'value="' . $tcValue . '"' . $tcAyuda . '/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
						else
							echo '<p><label>' . 
								'<input type="checkbox" id="' . $tcNombre . '" name="' . $tcNombre . '" ' . $tcAyuda . ' autofocus="autofocus"/>' . 
								'<span>' . $tcLabel . '</span>' .
								'</label></p>';
					}
				}
				break;

			case 'datepicker':
			case 'timepicker':
				// if	( ! empty($tcIcon) )
				// 	echo '<i class="' . $tcIcon . ' prefix"></i>';
				
				if	( $tlRO )
					echo '<input type="text" ' . $tcAyuda . ' class="validate ' . $tcTipo . '" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" maxlength="' . $tnTamano . '" value="' . $tcValue . '" readonly>';
				else
					echo '<input type="text" ' . $tcAyuda . ' class="validate ' . $tcTipo . '" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" maxlength="' . $tnTamano . '" value="' . $tcValue . '">';
				
				break;
				
			default:
				if	( $tcIcon == 'textsms')
					echo '<i class="material-icons prefix">textsms</i>';

				if	( $tlRO )
					echo '<input type="' . $tcTipo . '" ' . $tcAyuda . ' class="validate" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" maxlength="' . $tnTamano . '" value="' . $tcValue . '" readonly>';
				else
					if ($tcAutocomplete)
						echo '<input type="' . $tcTipo . '" ' . $tcAyuda . ' class="validate autocomplete" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" maxlength="' . $tnTamano . '" value="' . $tcValue . '">';
					else
						echo '<input type="' . $tcTipo . '" ' . $tcAyuda . ' class="validate" id="' . $tcNombre . '" ' . 'name="' . $tcNombre . '" maxlength="' . $tnTamano . '" value="' . $tcValue . '">';
		
				break;
		}
	
		if	( ! empty($tcLabel) AND $tcTipo <> 'checkbox' AND $tcTipo <> 'select' AND $tcTipo <> 'select multiple' AND $tcTipo <> 'file' )
		{
			echo '<label for="' . $tcNombre . '">' . $tcLabel . '</label>';
		}
		
		echo '</div>';
	}

	function getSelect($Parametro, $Valor, $Filtro, $Orden)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();
		
		$Select = '';

		$query = 'SELECT PARAMETROS.* ' . 
				'FROM PARAMETROS ' .
				'WHERE ' .
				"PARAMETROS.Parametro = '" . $Parametro . "' " . 
				( empty($Filtro) ? '' : ' AND ' . $Filtro . ' ' ) .
				'ORDER BY ' . $Orden;

		$data = $conn->listar($query);
	
		for ($i=0; $i < count($data); $i++) 
		{ 
			if	( $data[$i]['id'] == $Valor )
				$Select .= '<option selected value=' . $data[$i]['id'] . '>' . $data[$i]['detalle'] . '</option>';
			else
				$Select .= '<option value=' . $data[$i]['id'] . '>' . $data[$i]['detalle'] . '</option>';
		}
		
		return $Select;
	}

	function getSelectValor($Parametro, $Valor, $Filtro, $Orden)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();
		
		$Select = '';

		if (empty($Filtro))
			$query = <<<EOD
				SELECT PARAMETROS.* 
					FROM PARAMETROS 
					WHERE PARAMETROS.Parametro = '$Parametro' 
					ORDER BY $Orden;
			EOD;
		else
			$query = <<<EOD
				SELECT PARAMETROS.* 
					FROM PARAMETROS 
					WHERE PARAMETROS.Parametro = '$Parametro' 
						AND $Filtro) 
					ORDER BY $Orden;
			EOD;

		$data = $conn->listar($query);

		if ($data)
		{
			for ($i = 0; $i < count($data); $i++) 
			{ 
				if	( $data[$i]['valor'] == $Valor )
					$Select .= '<option selected value=' . $data[$i]['valor'] . '>' . $data[$i]['detalle'] . '</option>';
				else
					$Select .= '<option value=' . $data[$i]['valor'] . '>' . $data[$i]['detalle'] . '</option>';
			}
		}
		
		return $Select;
	}

	function getId($tcTabla, $tcFiltro)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();
		
		$query = 'SELECT ' . $tcTabla . '.id ' .
					'FROM ' . $tcTabla . ' ' .
					'WHERE ' . $tcFiltro;
					
		$reg = $conn->leer($query);
		
		return ( $reg ? $reg['id'] : 0);
	}

	function getTabla($tabla, $filtro, $orden)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();

		$query = 'SELECT ' . $tabla . '.* ' .
					'FROM ' . $tabla . ' ' .
					( empty($filtro) ? '' : 'WHERE ' . $filtro . ' ' ) .
					( empty($orden) ? '' : 'ORDER BY ' . $orden . ' ' );
	
		$data = $conn->listar($query);
			
		return $data;
	}		


	function validateFields($data){

		$response = "";
		
		if( empty($data['TipoIdentificacion']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de identificación.') . '</strong><br>';
		}
		
		if( empty($data['Documento']) ){
			$response .= label('Debe digitar un') . ' <strong>' . label('Documento') . '</strong><br>';
		}

		if( empty($data['FechaExpedicion']) ){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de expedición') . '</strong><br>';
		}elseif ($data['FechaExpedicion'] >= date('Y-m-d')){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de expedición correcta') . '</strong><br>';
		}
		if( empty($data['IdCiudadExpedicion']) ){
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de expedición') . '</strong><br>';
		}

		if( empty($data['Apellido1']) ){
			$response .= label('Debe digitar el primer') . ' <strong>' . label('Apellido') . '</strong><br>';
		}

		if( empty($data['Nombre1']) ){
			$response .= label('Debe digitar el primer') . ' <strong>' . label('Nombre') . '</strong><br>';
		}

		if	( empty($data['FechaNacimiento']) ){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento') . '</strong><br>';
		}elseif ($data['FechaNacimiento'] >= $data['FechaExpedicion']){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de nacimiento correcta (posterior a la Fecha de expedición)') . '</strong><br>';
		}

		if	( empty($data['IdCiudadNacimiento']) ){
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de nacimiento') . '</strong><br>';
		}

		if	( empty($data['Genero']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Género') . '</strong><br>';
		}

		if	( empty($data['EstadoCivil']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Estado civil') . '</strong><br>';
		}

		if	( empty($data['FactorRH']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Factor RH') . '</strong><br>';
		}

		if	( empty($data['Direccion']) ){
			$response .= label('Debe digitar una') . ' <strong>' . label('Dirección') . '</strong><br>';
		}

		if	( empty($data['Barrio']) ){
			$response .= label('Debe digitar un') . ' <strong>' . label('Barrio') . '</strong><br>';
		}
		
		if	( empty($data['IdCiudad']) ){
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad') . '</strong><br>';
		}

		if	( empty($data['Email']) ){
			$response .= label('Debe digitar el') . ' <strong>' . label('E-mail') . '</strong><br>';
		}elseif	( !filter_var($data['Email'], FILTER_VALIDATE_EMAIL) ){
			$response .= label('Formato invalido de') . ' <strong>' . label('Email') . '</strong><br>';
		}

		if	( empty($data['Celular']) ){
			$response .= label('Debe digitar el número de') . ' <strong>' . label('Celular') . '</strong><br>';
		}elseif (!is_numeric($data['Celular'])){
			$response .= label('Formato invalido de') . ' <strong>' . label('Celular') . '</strong><br>';
		}elseif (strlen($data['Celular']) < 10){
			$response .= '<strong>' . label('Celular') . ' </strong>'.label('debe tener al menos 10 digitos<br>');
		}

		if	(empty($data['IdCargo']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Cargo') . '</strong><br>';
		}
		
		if	( empty($data['IdCiudadTrabajo']) ){
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Ciudad de trabajo') . '</strong><br>';
		}
		
		if	(empty($data['IdCentro']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Centro de costos') . '</strong><br>';
		}

		if	(empty($data['Vicepresidencia']) ){	
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Vicepresidencia') . '</strong><br>';
		}
		
		if	(empty($data['IdSede']) ){
			$response .= label('Debe seleccionar una') . ' <strong>' . label('Sede') . '</strong><br>';
		}
		
		if	(empty($data['TipoContrato']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Tipo de contrato') . '</strong><br>';
		}
		
		if	(empty($data['ModalidadTrabajo']) ){
			$response .= label('Debe seleccionar un') . ' <strong>' . label('Modalidad de trabajo') . '</strong><br>';
		}
		
		if	(empty($data['FechaIngreso']) ){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong><br>';
		}elseif ($data['FechaIngreso'] < date('Y-m-d')){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de ingreso') . '</strong> posterior a hoy<br>';
		}
		
		if	(empty($data['FechaPeriodoPrueba']) ){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de período de prueba') . '</strong><br>';
		}elseif ($data['FechaPeriodoPrueba'] <= $_REQUEST['FechaIngreso']){
			$response .= label('Debe digitar una') . ' <strong>' . label('Fecha de período de prueba') . '</strong> posterior a la fecha de ingreso<br>';
		}

		return $response;

	}




	function validateDocumentFirmPLus($codefirma,$id){

		if($codefirma == "1" || $codefirma == "0" || $codefirma == "" || $codefirma == NULL){
			return true;
		}else{

			$curl = curl_init();

			curl_setopt_array($curl, 
				array(
					CURLOPT_URL => URL_FIRMA . '/consultarsolicitud/'.$codefirma,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'Token: ' . TOKEN_FIRMA,
						'Content-Type: application/json'
					),
				)
			);

			$response = json_decode(curl_exec($curl), true);

			curl_close($curl);

			if ($response['Code'] == 1){
				if($response["Data"]["Estado"] == "FIRMADO"){
					$querychange = <<<EOD
							UPDATE EMPLEADOS 
								SET CNT_ContratosFirmados = 1  
								WHERE EMPLEADOS.Id = $id;
						EOD;

						$conn = new pgSQL();
						$sql = $conn->query($querychange);
						
						return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}

		}

	}


	function getHoursMonth(){
		$rps = getRegistro("PARAMETROS",0,"PARAMETROS.parametro = 'HorasMes'");
		return intval($rps['detalle']);
	}

	function getRegistro($tabla, $id, $filtro = '')
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();

		if ($id > 0) 
		{
			$query = <<<EOD
				SELECT nomina.$tabla.* 
					FROM nomina.$tabla 
					WHERE nomina.$tabla.id = $id;
			EOD;
		}
		else
		{
			$query = <<<EOD
				SELECT $tabla.* 
					FROM $tabla 
					WHERE $filtro;
			EOD;
		}

		$reg = $conn->leer($query);

		if ($reg)
			return $reg;
		else
			return FALSE;
	}


	function logRequests($section, $body, $curl, $reponse, $type, $IdUsuario = "", $mailto = ""){
		
		try {

			require_once('libraries/core/pgSQL.php');
			$conn = new pgSQL();

			$dataLogin = '';
			$id_user = $IdUsuario == "" ? "" : $IdUsuario;
			if (isset($_SESSION['Login'])) {
				$dataLogin = implode(',', $_SESSION['Login']);
				$id_user =  $_SESSION['Login']['Id'];
			}

			
			$ip =  $_SERVER['REMOTE_ADDR'];
			$uri =  $_SERVER['REQUEST_URI'];
			$response =  $reponse;
			$date =  date('Y-m-d H:i:s');			
			$body = iconv('','UTF-8',$body);
			$query = <<<EOD
				INSERT INTO nomina.log_requests
				( id_user, typelog, ip, uri, sectionlog, body, curl, response, datelog, data_user, emailto)
				VALUES($id_user, '$type', '$ip', '$uri', '$section', '$body', '$curl', '$response', '$date', '$dataLogin', '$mailto');
			EOD;

			$sql = $conn->query($query);

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}
	

	function cancelardocumentos($id){
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();
		$empleado = getRegistro('EMPLEADOS', $id);
			$codeFirm = $empleado["solicitudfirma"];

			$curl = curl_init();

			curl_setopt_array(
				$curl, array(
					CURLOPT_URL => URL_FIRMA . "cancelarsolicitud/" . $codeFirm,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'PUT',
					CURLOPT_HTTPHEADER => array(
						'Token: ' . TOKEN_FIRMA,
						'accept: application/json, text/plain, */*',
						'Content-Type: application/json;charset=UTF-8',
						'Content-Length: 0'
					)
				)
			);

			

			$response = curl_exec($curl);
			

			$response = json_decode($response, true);

			logRequests("CONTATOS","",json_encode(curl_getinfo($curl)), json_encode($response), "CANCELAR FIRMA PLUS");
			curl_close($curl);

			if(isset($response["Code"])){
				if($response["Code"] == "1"){
					$query = <<<EOD
						UPDATE EMPLEADOS 
							SET solicitudfirma = 0 WHERE EMPLEADOS.Id = $id;
					EOD;

					$conn->query($query);

					return 'Documentos cancelados correctamente';					
				}else{
					return 'No se encontraron documentos a cancelar';			
				}				
			}else{
				return 'No se encontraron documentos a cancelar';
			}

	}

	function getLogs($section,$init,$end){
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();
		$query = <<<EOD
		SELECT COUNT(*) AS contador FROM nomina.log_requests 
		where typelog = 'FIRMA PLUS' AND datelog BETWEEN '$init' AND '$end';
		EOD;

		$reg = $conn->leer($query);

		if ($reg)
			return $reg["contador"];
		else
			return FALSE;

	}


	function execQuery($query)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();

		$ok = $conn->query($query);
			
		return $ok;
	}

	function buscarRegistro($tabla, $where)
	{
		require_once('libraries/core/pgSQL.php');
		$conn = new pgSQL();

		$query = <<<EOD
			SELECT $tabla.*
				FROM  $tabla 
				WHERE $where;
		EOD;

		$reg = $conn->leer($query);

		return $reg;
	}

	function left($str, $length) 
	{
		return substr($str, 0, $length);
	}

	function right($str, $length) 
	{
		return substr($str, -$length);
	}

	function ComienzoMes($tdFecha)
	{
		$tdFecha = date('Y-m-d', strtotime($tdFecha . '-' . date('d', strtotime($tdFecha)) . ' days'));
		return date('Y-m-d', strtotime($tdFecha . '+ 1 days'));
	}

	function ComienzoAno($tdFecha)
	{
		return (substr($tdFecha, 0, 4) . '-01-01');
	}

	function ComienzoSemestre($tdFecha)
	{
		if (date('m', strtotime($tdFecha)) >= 7)
			return (substr($tdFecha, 0, 4) . '-07-01');
		else
			return (substr($tdFecha, 0, 4) . '-01-01');
	}

	function FinMes($tdFecha)
	{
		return date('Y-m-d', mktime(0, 0, 0, date('m', strtotime($tdFecha)) + 1, 0, date('Y', strtotime($tdFecha))));
	}

	function Dias360($FechaFinal, $FechaInicial)
	{
		$AnoFinal = substr($FechaFinal, 0, 4);
		$MesFinal = substr($FechaFinal, 5, 2);
		$DiaFinal = substr($FechaFinal, 8, 2);

		$AnoInicial = substr($FechaInicial, 0, 4);
		$MesInicial = substr($FechaInicial, 5, 2);
		$DiaInicial = substr($FechaInicial, 8, 2);

		if ($FechaFinal < $FechaInicial)
			$Dias = 0;
		else
		{
			if ($DiaFinal == '31')
				$DiaFianl = '30';
				// $FechaFinal = substr($FechaFinal, 0, 8) . '30';

			if ($DiaFinal < $DiaInicial)
			{
				$Dias = $DiaFinal + 30 - $DiaInicial + 1;

				if ($MesFinal - 1 < $MesInicial) 
				{
					$Meses = $MesFinal + 11 - $MesInicial;
					$Anos = $AnoFinal - 1 - $AnoInicial;
				}
				else
				{
					$Meses = $MesFinal - 1 - $MesInicial;
					$Anos = $AnoFinal - $AnoInicial;
				}
			}
			else
			{
				$Dias = $DiaFinal - $DiaInicial + 1;
				if ($MesFinal < $MesInicial)
				{
					$Meses = $MesFinal + 12 - $MesInicial;
					$Anos = $AnoFinal - 1 - $AnoInicial;
				}
				else
				{
					$Meses = $MesFinal - $MesInicial;
					$Anos = $AnoFinal - $AnoInicial;
				}
			}
		
			$Dias = ($Anos * 360) + ($Meses * 30) + $Dias;
		}

		return $Dias;
	}

	function Dias365($FechaFinal, $FechaInicial)
	{
		if ($FechaFinal < $FechaInicial)
			$Dias = 0;
		else
		{
			$Fecha1 = new DateTime($FechaFinal);
			$Fecha2 = new DateTime($FechaInicial);
			$Dias = $Fecha1->diff($Fecha2)->days + 1;
		}

		return $Dias;
	}
	
	function montoEscrito($tnValor, $tlDinero = TRUE)
	{
		$lnValor = 0;
		$lcValorEnLetras = '';

		if ($tnValor < 0)
		{
			$lcValorEnLetras = 'MENOS ';
			$tnValor = abs($tnValor);
		}

		$lcNumero = right('000000000' . number_format($tnValor, 2, '.', ''), 12);

		if	( $tnValor == 0 )
			$lcValorEnLetras = 'Cero ';

		$lnValor = intval(left($lcNumero, 3));

		if	($lnValor > 0)
		{
			$lcValorEnLetras .= NumeroALetras($lnValor, $tlDinero);
			if	( $lnValor == 1 )
				$lcValorEnLetras .= 'Millón ';
			else
				$lcValorEnLetras .= 'Millones ';
			
			if	( intval(substr($lcNumero, 3, 6)) == 0 )
				$lcValorEnLetras .= 'De ';
		}

		$lnValor = intval(substr($lcNumero, 3, 3));

		if	( $lnValor > 0 )
		{
			$lcValorEnLetras .= NumeroALetras($lnValor, $tlDinero);
			$lcValorEnLetras .= 'Mil ';
		}

		$lnValor = intval(substr($lcNumero, 6, 3));

		if	( $lnValor > 0 )
			$lcValorEnLetras .= NumeroALetras($lnValor, $tlDinero);

		if	( $tlDinero )
		{
			if	( $tnValor == 1)
				$lcValorEnLetras .= 'Peso ';
			else
				$lcValorEnLetras .= 'Pesos ';
			$lcValorEnLetras .= 'Moneda Corriente';
		}
		else
		{
			$lcValorEnLetras .= 'Punto ';
			$lnValor = substr($lcNumero, 10, 1);
			if	( empty($lnValor) )
				$lcValorEnLetras .= 'Cero';
			else
				$lcValorEnLetras .= NumeroALetras(intval($lnValor), $tlDinero);
		}

		return $lcValorEnLetras;
	}

	function NumeroALetras($tnValor, $tlDinero)
	{

		if	( $tlDinero )
			$laUnidades[1] = 'Un ';
		else
			$laUnidades[1] = 'Uno ';
		$laUnidades[2] = 'Dos ';
		$laUnidades[3] = 'Tres ';
		$laUnidades[4] = 'Cuatro ';
		$laUnidades[5] = 'Cinco ';
		$laUnidades[6] = 'Seis ';
		$laUnidades[7] = 'Siete ';
		$laUnidades[8] = 'Ocho ';
		$laUnidades[9] = 'Nueve ';

		$laOnce[1] = 'Once ';
		$laOnce[2] = 'Doce ';
		$laOnce[3] = 'Trece ';
		$laOnce[4] = 'Catorce ';
		$laOnce[5] = 'Quince ';
		$laOnce[6] = 'Dieciseis ';
		$laOnce[7] = 'Diecisiete ';
		$laOnce[8] = 'Dieciocho ';
		$laOnce[9] = 'Diecinueve ';

		$laDecenas[1] = 'Diez ';
		$laDecenas[2] = 'Veinte ';
		$laDecenas[3] = 'Treinta ';
		$laDecenas[4] = 'Cuarenta ';
		$laDecenas[5] = 'Cincuenta ';
		$laDecenas[6] = 'Sesenta ';
		$laDecenas[7] = 'Setenta ';
		$laDecenas[8] = 'Ochenta ';
		$laDecenas[9] = 'Noventa ';

		$laCentenas[1] = 'Ciento ';
		$laCentenas[2] = 'Doscientos ';
		$laCentenas[3] = 'Trescientos ';
		$laCentenas[4] = 'Cuatrocientos ';
		$laCentenas[5] = 'Quinientos ';
		$laCentenas[6] = 'Seiscientos ';
		$laCentenas[7] = 'Setecientos ';
		$laCentenas[8] = 'Ochocientos ';
		$laCentenas[9] = 'Novecientos ';

		$lcValorEnLetras = '';

		if	( $tnValor >= 100 )
		{
			if	( $tnValor == 100 )
			{
				if	( $tlDinero )
					$lcValorEnLetras .= 'Un Cien ';
				else
					$lcValorEnLetras .= 'Cien ';
			}
			else
			{
				$lnDigito = intval($tnValor / 100);
				$lcValorEnLetras .= $laCentenas[$lnDigito];
			}
			$tnValor -= intval($tnValor / 100) * 100;
		}

		if	($tnValor >= 10 )
		{
			if	( $tnValor >= 11 AND $tnValor <= 19 )
			{
				$lnDigito = $tnValor - 10;
				$lcValorEnLetras .= $laOnce[$lnDigito];
				$tnValor = 0;
			}
			else
			{
				$lnDigito = intval($tnValor / 10);
				$lcValorEnLetras .= $laDecenas[$lnDigito];
				$tnValor -= intval($tnValor / 10) * 10;
				if	( $tnValor >= 1 )
					$lcValorEnLetras .= 'Y ';
			}
		}

		if	( $tnValor >= 1 )
			$lcValorEnLetras .= $laUnidades[$tnValor];

		return $lcValorEnLetras;
		
	}

	function Antiguedad($FechaIngreso, $FechaActual = '')
	{
		if (empty($FechaActual))
			$FechaActual = date('Y-m-d');

		$AnoIngreso = date('Y', strtotime($FechaIngreso));
		$MesIngreso = date('m', strtotime($FechaIngreso));
		$DiaIngreso = date('d', strtotime($FechaIngreso));

		$AnoActual = date('Y', strtotime($FechaActual));	
		$MesActual = date('m', strtotime($FechaActual));
		$DiaActual = date('d', strtotime($FechaActual));

		if ($DiaActual < $DiaIngreso) 
		{
			$Dias = $DiaActual + 31 - $DiaIngreso;

			if ($MesActual - 1 < $MesIngreso) 
			{
				$Meses = $MesActual + 11 - $MesIngreso;
				$Anos = $AnoActual - 1 - $AnoIngreso;
			}
			else
			{
				$Meses = $MesActual - 1 - $MesIngreso;
				$Anos = $AnoActual - $AnoIngreso;
			}
		}
		else
		{
			$Dias = $DiaActual - $DiaIngreso + 1;

			if ($MesActual < $MesIngreso) 
			{
				$Meses = $MesActual + 12 - $MesIngreso;
				$Anos = $AnoActual - 1 - $AnoIngreso;
			}
			else
			{
				$Meses = $MesActual - $MesIngreso;
				$Anos = $AnoActual - $AnoIngreso;
			}
		}

		$Antiguedad = $Anos . 'A ' . $Meses . 'M ' . $Dias . 'D';

		return $Antiguedad;
	}

	function generateCSV($fileName, $data, $headers=array()) {
		$output = fopen($fileName, 'w');

		$headers = isset($headers) ? $headers : array_keys($data[0]);
		$showHeaders = isset($headers) ? $headers : array_keys($data[0]);

		if (count($headers) == 0 AND count($data) > 0)
			foreach ($data[0] as $key => $value) {
				$headers[] = $key;
				$showHeaders[] = utf8_decode($key);
			}

		fputcsv($output, $showHeaders, ';');

		for ($i = 0; $i < count($data); $i++) {
			$current = $data[$i];
			$reg = array();

			foreach ($headers as $key) {
				$reg[$key] = isset($current[$key]) ? utf8_decode($current[$key]) : '';
			}

			fputcsv($output, $reg, ';');
		}

		fclose($output);

		header('Content-Description: File Transfer');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=' . basename($fileName));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($fileName));
		ob_clean();
		flush();
		readfile($fileName);
	}

	function cleanAccents($str) {
		return str_replace(
			array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ä','ë','ï','ö','ü','Ä','Ë','Ï','Ö','Ü','ñ','Ñ'), 
			array('A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','N', 'N'), 
		 	$str
		);
	}
?>
