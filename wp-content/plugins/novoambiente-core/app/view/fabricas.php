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
			
			$consulta = $registros->getRegistros('na_sys_fabricas', $paginate, array('campo'=>'label', 'tipo'=>'ASC'));
			
			$numeropaginas = ceil($consulta['total']/$porpagina);
			$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
			
			$until = mysql_num_rows($consulta['result']);
			$total = $consulta['total'];

			include("includes/fabricas-lista.php");

		break;
		case('produtos'):

			if(isset($_GET['value'])):

				$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;
				$porpagina = 8;
				$inicio = ($pagina-1)*$porpagina;
				$paginate = array('inicio' => $inicio, 'limite' => $porpagina);

				$tipos = $registros->getTiposByFabrica($_GET['value']);
				$produtos = $registros->getRegistros(DB_PRODUTOS." WHERE id_fabrica = ".$_GET['value']." ", $paginate, array('campo'=>'label', 'tipo'=>'ASC'));
				
				$dataTipos = array();
				while ($objTipos = mysql_fetch_object($tipos)):
					array_push($dataTipos, array( 'id' => $objTipos->id_tipo, 'nome' => $objTipos->label));
				endwhile;

				$dataProdutos = array();
				$produtosIDs = array();
				while ($objProdutos = mysql_fetch_object($produtos['result'])):
					array_push($dataProdutos, array(	'id' => $objProdutos->id_produto, 
														'nome' => $objProdutos->label,
														'id_fabrica' => $objProdutos->id_fabrica,
														'id_post' => $objProdutos->id_post));
					array_push($produtosIDs, $objProdutos->id_produto);
				endwhile;

				$produtosImplode = implode("','", $produtosIDs);
				$valores = $registros->getValores($produtosImplode);

				$numeropaginas = ceil($produtos['total']/$porpagina);
				$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
				
				$until = mysql_num_rows($produtos['result']);
				$total = $produtos['total'];

			else:

				$_SESSION['retorno']['classe'] = 'erro';
            	$_SESSION['retorno']['mensagem'] = 'Erro interno. Por favor, entre em contato com o administrador.';
			
			endif;

			include("includes/fabricas-produtos.php");

		break;
		case('editar'):

			if(isset($_GET['value'])):

				$fabrica = isset($_GET["fab"])?$_GET["fab"]:0;

				$tipos = $registros->getTiposByFabrica($fabrica);
				$produtos = $registros->getRegistros(DB_PRODUTOS." WHERE id_produto = ".$_GET['value']." ", array('inicio' => 0, 'limite' => 8), array('campo'=>'label', 'tipo'=>'ASC'));
				
				$dataTipos = array();
				while ($objTipos = mysql_fetch_object($tipos)):
					array_push($dataTipos, array( 'id' => $objTipos->id_tipo, 'nome' => $objTipos->label));
				endwhile;

				$dataProdutos = array();
				$produtosIDs = array();
				while ($objProdutos = mysql_fetch_object($produtos['result'])):
					array_push($dataProdutos, array(	'id' => $objProdutos->id_produto, 
														'nome' => $objProdutos->label,
														'id_fabrica' => $objProdutos->id_fabrica,
														'id_post' => $objProdutos->id_post));
					array_push($produtosIDs, $objProdutos->id_produto);
				endwhile;

				$produtosImplode = implode("','", $produtosIDs);
				$valores = $registros->getValores($produtosImplode);
				
				$until = mysql_num_rows($produtos['result']);
				$total = $produtos['total'];

			else:

				$_SESSION['retorno']['classe'] = 'erro';
            	$_SESSION['retorno']['mensagem'] = 'Erro interno. Por favor, entre em contato com o administrador.';
			
			endif;

			include("includes/fabricas-produtos-edit.php");

		break;
	endswitch;

?>

