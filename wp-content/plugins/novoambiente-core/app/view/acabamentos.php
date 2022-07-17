<?php 

	$registros = new registros;
	$registro = new registro;
	$url_redirect_page = $_GET["page"];
										
	$view = isset($_GET["view"]) ? $_GET["view"] : $view = 'lista';

	switch($view):
		case('lista'):

			$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;
			$porpagina = 100;
			$inicio = ($pagina-1)*$porpagina;
			$paginate = array('inicio' => $inicio, 'limite' => $porpagina);
			
			$consulta = $registros->getTiposAcabamentos();
						
			$numeropaginas = ceil($consulta['total']/$porpagina);
			$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
				
			$until = mysql_num_rows($consulta['result']);
			$total = $consulta['total'];

			include("includes/acabamento-lista.php");

		break;
	endswitch;
	
	//include '/var/www/_scripts/debug/debug.php';
?>