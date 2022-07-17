<?php
if( !isset($_SESSION) ){ session_start(); }

if(!empty($_FILES['produtos'])):
	
	ob_start();
	error_reporting(0);

	require_once ("../../../../../wp-config.php");
	if (defined('CONSTANT')){ define('PATH_SITE', ABSPATH); }

	include_once ( PATH_SITE."wp-content/plugins/novoambiente-core/system/core.php");
	require_once( PATH_SITE.'wp-config.php');

	/** Doing the connection */
	if (!defined('CONN'))
		define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );

	mysql_select_db( DB_NAME, CONN );

	//Primeiramente o arquivo XLS deve ser salvo em disco para poder ser manipulado pela classe XLS
	$produtos = $_FILES['produtos'];
	$registros = new Registros;
	$debug = '';

	//O nome do arquivo é concatenado com a hora atual, com precisão de segundos, para evitar perda de dados
	$file = $produtos['tmp_name'];
	$file_name = date('Y-m-d-s-i-H', time())."-".$produtos['name'];
	$file_path = PATH_SAVE_TEMP_FILES.$file_name;
	
	try{
		move_uploaded_file($produtos['tmp_name'], $file_path);
	}catch(Exception $e){
		error_log('Falha no upload do arquivo');
		error_log($e->getMessage(), 0);
	}
	
	//Gera um array a partir do arquivo XLS e transforma os cabeçalhos em objetos que serão usados em futuro insert ou update	

	$array_produtos = $registros->getArrayFromXLS($file_path, 'produtos'); //echo '<pre>'; print_r($array_produtos);
	
    if(!$array_produtos):

		$_SESSION['retorno']['classe'] = 'erro';
		$_SESSION['retorno']['mensagem'] = 'Erro na importação. Os cabeçalhos deverão ser definidos na 1ª linha da planilha.';
		
		//@TODO Aqui tem que setar as sessions de retorno
		header("location: ".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core");

	else:

		while($chave = array_search('', $registros->temp_headers)):
			//removendo indices vazios
			unset($registros->temp_headers[$chave]);
		endwhile;

		//Verifica se os cabeçalhos estão corretos
		$result = array_diff($registros->fix_headers_prod, $registros->temp_headers);
		$resultOutro = array_diff($registros->temp_headers, $registros->fix_headers_prod);

		if(empty($result)):

			//Traduz os campos para verificação e inclusão em banco de dados
			$cod_produto  = array_search('nome_produto', $registros->temp_headers);
			$cod_fabrica  = array_search('fabrica', $registros->temp_headers);
			$cod_designer = array_search('designer', $registros->temp_headers);
			
			$field_produto  = array_search('nome_produto', $registros->produtos);
			$field_fabrica  = array_search('fabrica', $registros->fabricas);
			$field_designer = array_search('designer', $registros->designers); 
			
			//Mapeia em quais campos do array principal, estão os dados de insert ou update
			$registros->map['fabrica']['label'] = $cod_fabrica;
			$registros->map['fabrica']['gid'] 	= $cod_fabrica;
			
			$registros->map['designer']['nome'] = $cod_designer;
			$registros->map['designer']['gid'] 	= $cod_designer;
            
            $registros->map['produtos']['cod_produto'] = array_search('codigo', $registros->temp_headers);
			$registros->map['produtos']['label'] = array_search('nome_produto', $registros->temp_headers);
			$registros->map['produtos']['descricao'] = array_search('descricao_produto', $registros->temp_headers);

        	$registros->map['variacoes']['altura'] = array_search('altura', $registros->temp_headers);

			$registros->map['variacoes']['comprimento'] = array_search('comprimento', $registros->temp_headers);
			$registros->map['variacoes']['profundidade'] = array_search('profundidade', $registros->temp_headers);
			$registros->map['variacoes']['foto1'] = array_search('foto1', $registros->temp_headers);
			$registros->map['variacoes']['foto2'] = array_search('foto2', $registros->temp_headers);
			$registros->map['variacoes']['foto3'] = array_search('foto3', $registros->temp_headers);
			$registros->map['variacoes']['foto4'] = array_search('foto4', $registros->temp_headers);
			$registros->map['variacoes']['foto5'] = array_search('foto5', $registros->temp_headers);
			$registros->map['variacoes']['novidade'] = array_search('novidade', $registros->temp_headers);
			$registros->map['variacoes']['medida_especial'] = array_search('medida_especial', $registros->temp_headers);
			$registros->map['variacoes']['low_cost'] = array_search('low_cost', $registros->temp_headers);
			$registros->map['variacoes']['in_ecommerce'] = array_search('in_ecommerce', $registros->temp_headers);
			$registros->map['variacoes']['ecommerce_link'] = array_search('ecommerce_link', $registros->temp_headers);
			$registros->map['variacoes']['descricao'] = array_search('descricao_variacao', $registros->temp_headers);
				
			//Para cada linha do array é feita uma verificação se o produto ja existe, caso exista será apenas atualizado
			$registros->atualizados['produtos'] = array();
			$registros->atualizados['variacoes'] = array();
			$registros->atualizados['designers'] = array();
			$registros->atualizados['fabricas'] = array();

			$registros->incluidos['produtos'] = array();
			$registros->incluidos['variacoes'] = array();
			$registros->incluidos['designers'] = array();
			$registros->incluidos['fabricas'] = array();

			// Arrays que guardarão os dados que já foram inseridos no DB
			$varFab = array();
			$varPro = array();
			$varDes = array();
			$varVar = array();

			$registros->incluidos['total'] = 0;
			$registros->atualizados['total'] = 0;
			$itensFabrica = 0;
			
			$debug .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
						"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
						<html>
						  <head>
						    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
						    <title>Debug de importação | Novo Ambiente</title>
						    <style type="text/css">
								.table {
								   font-size:10pt;
								   font-family:sans-serif, arial;
								    border:-color: #CDC5BF;
								   border-width: 1px;
								   border-style: solid;
								 }
							</style>
						  </head>
						<body>
							<table class="table" align="center" width="800"><tr><td width="200">&nbsp;</td><td width="600">&nbsp;</td></tr>';
			
			$headresEncontrados = implode(", ", $registros->temp_headers);
			$debug .= "<tr><td colspan='2'>Headers encontrados: ".$headresEncontrados."</td></tr>";

			$debugLlinhas = 0;

			foreach($array_produtos as $item => $dados):
					
					//print_r($dados); die;
					//$dados = strip_tags($dados);

				if(!empty($dados[$registros->map['fabrica']['label']])):

					$debug .= "<tr><td colspan='2' bgcolor='#CDC5BF'> Linha ".$debugLlinhas." | Produto ".$dados[$registros->map['produtos']['label']]."</td></tr>";
					
					// Obtem os dados atuais para conferir se estes já não passaram pelo loop
					$varFabAtual = isset($dados[$registros->map['fabrica']['label']])?$registros->makeSlug($dados[$registros->map['fabrica']['label']]):'';
					$varProAtual = array(	'nome' => isset($dados[$registros->map['produtos']['label']])?$registros->makeSlug($dados[$registros->map['produtos']['label']]):'',
										'codigo' => isset($dados[$registros->map['produtos']['cod_produto']])?$registros->makeSlug($dados[$registros->map['produtos']['cod_produto']]):'');		
					$varDesignerAtual = isset($dados[$registros->map['designer']['nome']])?$registros->makeSlug($dados[$registros->map['designer']['nome']]):'';
					$varVarAtual = array(	'produto' => isset($dados[$registros->map['produtos']['label']])?$registros->makeSlug($dados[$registros->map['produtos']['label']]):'', 
										'altura' => isset($dados[$registros->map['variacoes']['altura']])?$dados[$registros->map['variacoes']['altura']]:'', 
										'comprimento' => isset($dados[$registros->map['variacoes']['comprimento']])?$dados[$registros->map['variacoes']['comprimento']]:'', 
										'profundidade' => isset($dados[$registros->map['variacoes']['profundidade']])?$dados[$registros->map['variacoes']['profundidade']]:'', 
										'descricao' => isset($dados[$registros->map['variacoes']['descricao']])?$dados[$registros->map['variacoes']['descricao']]:'');
					
					// Só atualiza/insere uma fábrica se o gid não constar no array
					$registros->tabela = 'na_sys_fabricas';
					$resultBdFarica = $registros->atualizaBase($dados, $field_fabrica, $registros->makeSlug($cod_fabrica), 'atualizaFabrica');
						
					if($resultBdFarica['resultado']):
						$degubFabrica = ($resultBdFarica['consulta']=='insert')?'Inserido no DB.':'Atualizado no DB.';
					else:
						$degubFabrica = 'Erro na consulta ao DB.';
						$registros->map['produtos']['id_fabrica'] = 0;
					endif;

					array_push($varFab, $registros->makeSlug($dados[$registros->map['fabrica']['label']]));

					$debug .= "<tr><td> Insere fábrica -> ".$degubFabrica."</td><td> Nome: ".$varFabAtual." </td></tr>";

					// SE O DESIGNER ESTIVER NO BANCO, INSERE O ID DO DESIGNER NO PRODUTO.
					// ELSE SE O DESIGNER FOR NOVO, CRIA O DESIGNER, PEGA O ID E ADICIONA
					$registros->tabela = 'na_sys_designers';
					//echo "<h2>".@print_r($dados[6], true)."</h2>";

					$resultBdDesign = $registros->atualizaBase($dados, $field_designer, $registros->makeSlug($cod_designer), 'atualizaDesigner');
					
					//print_r($resultBdDesign);

					if($resultBdDesign['resultado']):
						$debugDesign = ($resultBdDesign['consulta']=='insert')?'Inserido no DB.':'Atualizado no DB.';
					else:
						$debugDesign = 'Erro na consulta ao DB';
						$registros->map['produtos']['id_designer'] = 0;
					endif;	
					//echo "<h3>".$debugDesign."</h3>";					

					array_push($varDes, isset($dados[$registros->map['designer']['nome']])?$registros->makeSlug($dados[$registros->map['designer']['nome']]):'');
					$debug .= "<tr><td> Insere designer -> ".$debugDesign."</td><td> Nome: ".$varDesignerAtual." </td></tr>";

					
					// Só atualiza/insere um produto se o gid não constar no array
					if(!in_array($varProAtual, $varPro)):
						//print "******* Designer chegou no produto: ".$dados[$registros->map['produtos']['label']]." como: ".$registros->map['produtos']['id_designer'] ."<br>";
						$nomeProduto = (isset($dados[$registros->map['produtos']['label']])&&!empty($dados[$registros->map['produtos']['label']]))?$registros->makeSlug($dados[$registros->map['produtos']['label']]):'';
						$codProduto = (isset($dados[$registros->map['produtos']['cod_produto']])&&!empty($dados[$registros->map['produtos']['cod_produto']]))?$dados[$registros->map['produtos']['cod_produto']]:'';

						$registros->tabela = 'na_sys_produtos';
						$resultBdProduto =  $registros->atualizaBase($dados, $field_produto, $registros->makeSlug($cod_produto), 'atualizaProduto');
						
						if($resultBdProduto['resultado']):
							$debugProduto = ($resultBdProduto['consulta']=='insert')?'Inserido no DB.':($resultBdProduto['consulta']=='update')?'Atualizado no DB.':'Erro no envio para o DB';
						else:
							$debugProduto = 'Erro na consulta ao DB';
							$registros->map['produtos']['id_produto'] = 0;
						endif;

						array_push($varPro, array( 	'nome' => $nomeProduto,
												'codigo' => $codProduto));

						$debug .= "<tr><td> Insere produto -> ".$debugProduto."</td><td> Nome: ".$nomeProduto." | Código: ".$codProduto." </td></tr>";

					else:
						$debug .= "<tr><td colspan='2'> Produto já foi cadastrado antes - Nome: ".$nomeProduto." | Código: ".$codProduto." </td></tr>";
						
					endif;


					$debug .= "<tr><td colspan='2'> Imagens encontradas:<br />
					 Foto1: ".$dados[$registros->map['variacoes']['foto1']]."<br />
					 Foto2: ".$dados[$registros->map['variacoes']['foto2']]."<br />
					 Foto3: ".$dados[$registros->map['variacoes']['foto3']]."<br />
					 Foto4: ".$dados[$registros->map['variacoes']['foto4']]."<br />
					 Foto5: ".$dados[$registros->map['variacoes']['foto5']]."</td></tr>";

					
					// Só atualiza/insere uma variação se as dimensões e o nome do produto não constarem no array
					if(!in_array($varVarAtual, $varVar)):
						
						$tipVarProdLabel = (isset($dados[$registros->map['produtos']['label']])&&!empty($dados[$registros->map['produtos']['label']]))?$registros->makeSlug($dados[$registros->map['produtos']['label']]):'';
						$tipVarCodProduto = (isset($dados[$registros->map['produtos']['cod_produto']])&&!empty($dados[$registros->map['produtos']['cod_produto']]))?$dados[$registros->map['produtos']['cod_produto']]:'';
						$tipVarAltura = (isset($dados[$registros->map['variacoes']['altura']])&&!empty($dados[$registros->map['variacoes']['altura']]))?$dados[$registros->map['variacoes']['altura']]:'';
						$tipVarComprimento = (isset($dados[$registros->map['variacoes']['comprimento']])&&!empty($dados[$registros->map['variacoes']['comprimento']]))?$registros->makeSlug($dados[$registros->map['variacoes']['comprimento']]):'';
						$tipVarProfundidade = (isset($dados[$registros->map['variacoes']['profundidade']])&&!empty($dados[$registros->map['variacoes']['profundidade']]))?$registros->makeSlug($dados[$registros->map['variacoes']['profundidade']]):'';
						$tipVarDescricao = (isset($dados[$registros->map['variacoes']['descricao']])&&!empty($dados[$registros->map['variacoes']['descricao']]))?$registros->makeSlug($dados[$registros->map['variacoes']['descricao']]):'';

						$registros->tabela = 'na_sys_produtos_variacoes';
						$resultBdVariacao = $registros->atualizaDetalhesProduto($dados);

						$arrAtulTeste = array(	'produto' => $tipVarProdLabel, 
												'altura' => $tipVarAltura, 
												'comprimento' => $tipVarComprimento, 
												'profundidade' => $tipVarProfundidade, 
												'descricao' => $tipVarDescricao);
						
						array_push($varVar, array(	'produto' => $tipVarProdLabel, 
												'altura' => $tipVarAltura, 
												'comprimento' => $tipVarComprimento, 
												'profundidade' => $tipVarProfundidade, 
												'descricao' => $tipVarDescricao));

						if($resultBdVariacao['resultado']):
							$degubVariacao = ($resultBdVariacao['consulta']=='insert')?'Inserido no DB.':($resultBdVariacao['consulta']=='update')?'Atualizado no DB.':'Erro no envio para o DB';
						else:
							$degubVariacao = 'Erro na consulta ao DB';
							$registros->map['produtos']['id_produto'] = 0;
						endif;

						$debug .= "<tr><td> Insere variação -> ".$degubVariacao."</td>
								<td> 	Produto: ".$tipVarProdLabel." | 
										Dimensões: ".$tipVarAltura."x".$tipVarComprimento."x".$tipVarProfundidade." |
										Descrição: ".$tipVarDescricao."</td></tr>";

					else:
						$debug .= "<tr><td colspan='2'> Variação já foi cadastrada antes - Nome: ".$tipVarProdLabel." | Código: ".$tipVarCodProduto." </td></tr>";
					endif;

				else:
					$itensFabrica++;
				endif;
				
				$debugLlinhas++;

			endforeach;

			$st_produto = $registros->strDetalhes(count($registros->incluidos['produtos']), count($registros->atualizados['produtos']), 'produto', 'produtos', 'o');
			$st_variacoes = $registros->strDetalhes(count($registros->incluidos['variacoes']), count($registros->atualizados['variacoes']), 'variação', 'variações', 'a');
			$st_designers = $registros->strDetalhes(count($registros->incluidos['designers']), count($registros->atualizados['designers']), 'designer', 'designers','o');
			$st_fabrica = $registros->strDetalhes(count($registros->incluidos['fabricas']), count($registros->atualizados['fabricas']), 'f&aacute;brica', 'f&aacute;bricas', 'a');
			
			$_SESSION['retorno']['classe'] = 'sucesso';
			$_SESSION['retorno']['mensagem'] = 'Xls importado com sucesso. '.$registros->incluidos['total'].' novas ocorrências e '.$registros->atualizados['total'].' ocorrências atualizadas.';
			$_SESSION['retorno']['detalhes'] = $st_produto.$st_variacoes.$st_designers.$st_fabrica;

			if($itensFabrica>0):
				$_SESSION['retorno']['mensagem'] .= "<br> ".$itensFabrica." itens não foram importados pois não foi encontrado a informação <strong>fábrica</strong>.";
				$debug .= "<tr>
							<td colspan='3'> ".$itensFabrica." itens não foram importados pois não foi encontrado a informação <strong>fábrica</strong>. </td>
						</tr>";
			endif;
			
			$debug .= "</body></html>";
			//print $debug; die;
			
			$registros->enviouPlanilha($debug);

			//@TODO Aqui tem que setar as sessions de retorno
			header("location: ".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core");
			

		else:

			$str_fix_headers_prod = implode (' | ', $registros->fix_headers_prod);
			$camposDiferentes = implode (', ', $result);
			$camposDiferentesTabela = implode (', ', $resultOutro);

			$_SESSION['retorno']['classe'] = 'erro';
			$_SESSION['retorno']['mensagem'] = 'Erro na importação. Os cabeçalhos da planilha não estão de acordo com o padrão: '.$str_fix_headers_prod;
			
			if(strlen($camposDiferentes)>2):
				$_SESSION['retorno']['mensagem'] .= "<br><br><strong> Colunas não encontradas no arquivo: ".$camposDiferentes."</strong>";
			endif;

			if(strlen($camposDiferentesTabela)>2):
				$_SESSION['retorno']['mensagem'] .= "<br><br><strong> Colunas que serão ignoradas: ".$camposDiferentesTabela."</strong>";
			endif;
			
			//@TODO Aqui tem que setar as sessions de retorno
			header("location: ".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core");
			//die();

		endif;

	endif;

	ob_end_clean();

endif;

