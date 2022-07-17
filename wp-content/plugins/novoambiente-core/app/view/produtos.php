<?php 
	
	/*print "GET: <pre>";
	print_r($_GET);
	print "<hr> POST:";
	print_r($_POST);
	print "</pre>";*/

	$registros = new registros;
	$registro = new registro;
	$url_redirect_page = $_GET["page"];
										
	$view = isset($_GET["view"]) ? $_GET["view"] : 'lista';

	$listaProduto = isset($_GET["lista"]) ? $_GET["lista"] : 'inativos';

	if(isset($_POST['massAction'])&&$_POST['massAction']!='0'):

		if($_POST['massAction']=='deletar'):

			if(isset($_POST['idsProdutos'])):
			
				$size = strlen($_POST['idsProdutos']);
				$idsProdutos = substr($_POST['idsProdutos'],0, $size-1);
				$arrIds = explode(",", $idsProdutos);
				
				$cnt = 0;
				foreach($arrIds as $value):
					$registro->delProduto($value);
					$cnt++;
				endforeach;

				$_SESSION['retorno']['mensagem'] = 	$cnt.' produtos excluídos.';
				$_SESSION['retorno']['class'] = 'sucesso';

			else:

				$_SESSION['retorno']['mensagem'] = 'Produtos não excluídos, por favor entre em contato com o administrador.';
				$_SESSION['retorno']['class'] = 'erro';

			endif;

		elseif($_POST['massAction']=='catalogo'):

			if(isset($_POST['idsProdutos'])&&(strlen($_POST['idsProdutos'])>1)):

				$size = strlen($_POST['idsProdutos']);
				$idsProdutos = substr($_POST['idsProdutos'],0, $size-1);
				$arrIds = explode(",", $idsProdutos);

				$cnt = 0;

				// Para cada ID encontrado no loop
				foreach($arrIds as $key => $value):
				

						$label = 'id_produto';

						$consulta = $registros->getRegistro(DB_PRODUTOS, $label, $value);

						$arrProduto['id_produto'] = $value;
						$arrProduto['nome'] = $consulta['registro']->label;
						$arrProduto['descricao'] = $consulta['registro']->descricao;

						// Se possui fábrica cadastradas
						if(isset($consulta['registro']->id_fabrica)&&!empty($consulta['registro']->id_fabrica)):
							$objFabrica = $registros->getRegistro(DB_FABRICAS, 'id_fabrica', $consulta['registro']->id_fabrica);
							$arrProduto['fabrica'] = $objFabrica['registro']->label;
						else:
							$arrProduto['fabrica'] = '';
						endif;


						// busca as variações. Pode buscar só a primeira porque as imagens são iguais em todas as linhas do mesmo produto.
						$paginate = array('inicio' => '0', 'limite' => '1');
						$c_variacao = $registros->getRegistros('na_sys_produtos_variacoes WHERE id_produto = '.$value, $paginate, array('campo' => 'id_produto', 'tipo' => 'DESC'));

						// se encontrou variacao e é maior que 0
						if(isset($c_variacao['total'])&&$c_variacao['total']>0):
							//zera oS arrayS pra não repetir
							$arrImagensVariacoes = array();
							// Correção para não repetir imagens.
							$arrProduto['img'] = array();
							// Como só precisamos de 1 linha, vou definir o objeto da variação 1x
							$ov = mysql_fetch_object($c_variacao['result']);
							
							// Se não tiver nenhuma foto
							if(empty($ov->foto1)&&empty($ov->foto2)&&empty($ov->foto3)&&empty($ov->foto4)&&empty($ov->foto5)):
								$arrProduto['img_result'] = false;

							// Se não tiver alguma foto
							else:
								// faz um loop para pegar as 5 imagens do banco (os campos foto1, foto2, foto3, foto4 e foto5).
								for($i = 1; $i <= 5; $i++):
									// descobre o nome do campo atual
									$labelCampoFotoAtual = 'foto'.$i;
									// Se não existir o label atual no array..
									if(!empty($ov->$labelCampoFotoAtual)):											
										// Adiciona ao arrProduto a img atual.
										$arrProduto['img'][$labelCampoFotoAtual] = $ov->$labelCampoFotoAtual;
										// E adiciona ao array $arrImagensVariacoes
										array_push($arrImagensVariacoes, $ov->$labelCampoFotoAtual);
									endif;
								endfor;

								$arrProduto['img_result'] = true;
							endif;
								
						else:
							$arrProduto['img_result'] = false;
						endif;

						$registro->createPostProduto($arrProduto);
						$cnt++;

				endforeach;

				//die;

				$_SESSION['retorno']['mensagem'] = 	$cnt.' produtos publicados no <a href="'.get_bloginfo('url').'/wp-admin/edit.php?post_type=produto">catálogo</a>.';
				$_SESSION['retorno']['class'] = 'sucesso';

			else:

				$_SESSION['retorno']['mensagem'] = 'Produtos não publicados, por favor entre em contato com o administrador.';
				$_SESSION['retorno']['class'] = 'erro';

			endif;

		endif;
		

	endif;

	if(isset($_GET['action'])):
		switch($_GET['action']):
			case('editProd'):
				$registro->setInfosProduto($_POST);
			break;
			case('delProduto'):
				$registro->delProduto($_POST['id_produto']);
			break;
		endswitch;
	endif;

	switch($view):
		case('lista'):

			$filt_fabrica = !empty($_POST["fabrica"]) ? $_POST["fabrica"] : 'Todas as fábricas';
			
			$tipoLista = ($listaProduto=='inativos') ? DB_PRODUTOS.' WHERE id_post = 0 ' : DB_PRODUTOS.' WHERE  id_post <> 0 ';
			$tipoLista .= (is_numeric($filt_fabrica)) ? ' AND id_fabrica = '.$filt_fabrica : '';

			$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;
			$porpagina = 100;
			$inicio = ($pagina-1)*$porpagina;
			$paginate = array('inicio' => $inicio, 'limite' => $porpagina);
			
			$consulta = $registros->getRegistros( $tipoLista, $paginate, array('campo'=>'label', 'tipo'=>'ASC'));
			
			$numeropaginas = ceil($consulta['total']/$porpagina);
			$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
			
			$until = mysql_num_rows($consulta['result']);
			$total = $consulta['total'];

			include("includes/produto-lista.php");

		break;
		case('detalhe'):

			$label = isset($_GET["label"]) ? $_GET["label"] : $label = 'id_produto';
			$value = isset($_GET["value"]) ? $_GET["value"] : $value = NULL;

			$paginate = array('inicio' => 0, 'limite' => '100000');
			$consulta = $registros->getRegistro(DB_PRODUTOS, $label, $value);
			$c_variacao = $registros->getRegistros(DB_VARIACOES.' WHERE id_produto = '.$consulta['registro']->id_produto, $paginate, array('campo' => 'id_produto', 'tipo' => 'DESC'));

			include("includes/produto-detalhe.php");
			
		break;
		case('detalheVariacao'):

			$label = isset($_GET["label"]) ? $_GET["label"] : $label = 'id_variacao';
			$value = isset($_GET["value"]) ? $_GET["value"] : $value = NULL;
			$idProd = isset($_GET["idProd"]) ? $_GET["idProd"] : $idProd = NULL;
			$action = isset($_GET["action"]) ? $_GET["action"] : $action = NULL;

			if(isset($_GET['del'])):
				
				$registro = new registro;
				$infProduto = $registro->delVariacaoProduto($value, $_GET['idPost']);
				die();

			else:
				
				$infProduto = $registros->getRegistro(DB_PRODUTOS, 'id_produto', $idProd);
				
				if($action == 'adicionar'):
					
					//Ação de adicionar novo
					$id_produto_add = $idProd;
					$consulta = false;
				else:
					$consulta = $registro->getVariacaoByID($value);
				endif;

				include("includes/detalhe-variacao.php");

			endif;
			
		break;
	endswitch;
	
	//include '/var/www/_scripts/debug/debug.php';
?>