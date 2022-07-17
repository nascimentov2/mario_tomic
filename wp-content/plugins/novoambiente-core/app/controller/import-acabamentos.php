<?php
if( !isset($_SESSION) ){ session_start(); }

if(!empty($_FILES['acabamentos'])):
	
	ob_start();

	require_once ("../../../../../wp-config.php");
	define('PATH_SITE', ABSPATH);

	include_once ( PATH_SITE."wp-content/plugins/novoambiente-core/system/core.php");
	require_once( PATH_SITE.'wp-config.php');


	/** Doing the connection */
	if (!defined('CONN'))
		define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
	mysql_select_db( DB_NAME, CONN );

	//Primeiramente o arquivo XLS deve ser salvo em disco para poder ser manipulado pela classe XLS
	$acabamentos = $_FILES['acabamentos'];
	$registros = new Registros;
	
	//O nome do arquivo é concatenado com a hora atual, com precisão de segundos, para evitar perda de dados
	$file = $acabamentos['tmp_name'];
	$file_name = $acabamentos['name'].'-'.date('Y-m-d-s-i-H', time());
	$file_path = PATH_SAVE_TEMP_FILES.$file_name;
	
	try{
		move_uploaded_file($acabamentos['tmp_name'], $file_path);
	}catch(Exception $e){
		error_log('Falha no upload do arquivo');
		error_log($e->getMessage(), 0);
	}
	
	//Gera um array a partir do arquivo XLS e transforma os cabeçalhos em objetos que serão usados em futuro insert ou update	
	$array_acabamentos = $registros->getArrayFromXLS($file_path, 'produtos'); 
	
	while($chave = array_search('', $registros->temp_headers)):
		//removendo indices vazios
		unset($registros->temp_headers[$chave]);
	endwhile;

	//Verifica se os cabeçalhos estão corretos
	$result = array_diff($registros->fix_headers_acab, $registros->temp_headers);
	$resultOutro = array_diff($registros->temp_headers, $registros->fix_headers_acab);

	if(empty($result)):

		//Traduz os campos para verificação e inclusão em banco de dados
		$field_acabamento  = array_search('acabamento', $registros->acabamentos);
		$field_tipo  = array_search('tipo_acabamento', $registros->tipos_acabamentos);
		$field_grupo  = array_search('grupo_acabamento', $registros->grupos_acabamentos);
		$field_fabrica  = array_search('fabrica', $registros->fabricas);

		$cod_fabrica  = array_search('fabrica', $registros->temp_headers);
		$cod_acabamento  = array_search('acabamento', $registros->temp_headers);
		$cod_tipo  = array_search('tipo_acabamento', $registros->temp_headers);
		$cod_grupo  = array_search('grupo_acabamento', $registros->temp_headers);

		//Mapeia em quais campos do array principal, estão os dados de insert ou update
		$registros->map['acabamentos']['gid'] = array_search('acabamento', $registros->temp_headers);
		$registros->map['acabamentos']['label'] = array_search('acabamento', $registros->temp_headers);
		$registros->map['acabamentos']['detalhes'] = array_search('detalhes', $registros->temp_headers);
		$registros->map['acabamentos']['url_imagem'] = array_search('url_imagem', $registros->temp_headers);
		
		$registros->map['tipos_acabamentos']['gid'] = array_search('tipo_acabamento', $registros->temp_headers);
		$registros->map['tipos_acabamentos']['label'] = array_search('tipo_acabamento', $registros->temp_headers);
		
		$registros->map['grupos_acabamentos']['gid'] = array_search('grupo_acabamento', $registros->temp_headers);
		$registros->map['grupos_acabamentos']['label'] = array_search('grupo_acabamento', $registros->temp_headers);
		
		$registros->map['fabrica']['label'] = $cod_fabrica;
		$registros->map['fabrica']['gid'] 	= $cod_fabrica;

		/*
		echo "<pre>";	
		print_r($registros->map);
		die();
		*/

		//Para cada linha do array é feita uma verificação se o produto ja existe, caso exista será apenas atualizado
		$registros->atualizados['acabamentos'] = array();
		$registros->atualizados['tipos'] = array();
		$registros->atualizados['grupos'] = array();
		$registros->atualizados['fabricas'] = array();

		$registros->incluidos['acabamentos'] = array();
		$registros->incluidos['tipos'] = array();
		$registros->incluidos['grupos'] = array();
		$registros->incluidos['fabricas'] = array();

		// Arrays que guardarão os dados que já foram inseridos no DB
		$varFab = array();
		$varGru = array();
		$varTip = array();
		$varAca = array();

		$registros->incluidos['total'] = 0;
		$registros->atualizados['total'] = 0;

		foreach($array_acabamentos as $item => $dados):
				
				//$dados = strip_tags($dados);
				// [1] => Schuster [2] => Madeira [3] => Carvalho [4] => Cor 03
				// Obtem os dados atuais para conferir se estes já não passaram pelo loop
				$varFabAtual = $registros->makeSlug($dados[1]);
				$varGruAtual = $registros->makeSlug($dados[2]);			
				$varTipAtual = $registros->makeSlug($dados[3]);
				$varAcaAtual = $registros->makeSlug($dados[4].$dados[2].$dados[3]);
				
				// Só atualiza/insere um grupo se o gid não constar no array
				if(!in_array($varGruAtual, $varGru)):
					$registros->tabela = DB_ACABAMENTOS_GRUPOS;
					if(!$registros->atualizaBase($dados, $field_grupo, $cod_grupo, 'atualizaGruposAcabamentos'))
						$registros->map['acabamentos']['id_grupo'] = 0;
					array_push($varGru, $registros->makeSlug($dados[2]));
				endif;

				// Só atualiza/insere um tipo se o gid não constar no array
				if(!in_array($varTipAtual, $varTip)):
					$registros->tabela = DB_ACABAMENTOS_TIPOS;
					if(!$registros->atualizaBase($dados, $field_tipo, $cod_tipo, 'atualizaTiposAcabamentos'))
						$registros->map['acabamentos']['id_tipo'] = 0;
					array_push($varTip, $registros->makeSlug($dados[3]));
				endif;

				// Só atualiza/insere uma fábrica se o gid não constar no array
				if(!in_array($varFabAtual, $varFab)):
					$registros->tabela = DB_FABRICAS;
					if(!$registros->atualizaBase($dados, $field_fabrica, $registros->makeSlug($cod_fabrica), 'atualizaFabrica'))
						$registros->map['produtos']['id_fabrica'] = 0;
					array_push($varFab, $registros->makeSlug($dados[1]));
				endif;
				
				// Só atualiza/insere um acabamento se o gid não constar no array
				if(!in_array($varAcaAtual, $varAca)):
					$registros->tabela = DB_ACABAMENTOS;
					if(!$registros->atualizaBase($dados, $field_acabamento, $cod_acabamento, 'atualizaAcabamentos', 'acabamentos')):
						$_SESSION['retorno']['classe'] = 'erro';
						$_SESSION['retorno']['mensagem'] = 'Erro interno. Por favor entre em contato com o administrador.';
					endif;
					array_push($varAca, $registros->makeSlug($dados[4].$dados[2].$dados[3]));
				endif;

		endforeach;
		
		$st_grupo = $registros->strDetalhes(count($registros->incluidos['grupos']), count($registros->atualizados['grupos']), 'grupo', 'grupos', 'o');
		$st_tipo = $registros->strDetalhes(count($registros->incluidos['tipos']), count($registros->atualizados['tipos']), 'tipo', 'tipos', 'o');
		$st_fabrica = $registros->strDetalhes(count($registros->incluidos['fabricas']), count($registros->atualizados['fabricas']), 'f&aacute;brica', 'f&aacute;bricas', 'a');
		$st_acabamento = $registros->strDetalhes(count($registros->incluidos['acabamentos']), count($registros->atualizados['acabamentos']), 'acabamento', 'acabamentos', 'o');
		
		$_SESSION['retorno']['classe'] = 'sucesso';
		$_SESSION['retorno']['mensagem'] = 'Xls importado com sucesso. '.$registros->incluidos['total'].' novas ocorrências e '.$registros->atualizados['total'].' ocorrências atualizadas.';
		$_SESSION['retorno']['detalhes'] = $st_acabamento.$st_grupo.$st_tipo.$st_fabrica;
		//@TODO Aqui tem que setar as sessions de retorno
		header("location: ".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_acabamentos");
		//die();

	else:

		$str_fix_headers_acab = implode (' | ', $registros->fix_headers_acab);
		$camposDiferentes = implode (', ', $result);
		$camposDiferentesTabela = implode (', ', $resultOutro);

		$_SESSION['retorno']['classe'] = 'erro';
		$_SESSION['retorno']['mensagem'] = 'Erro na importação. Os cabeçalhos da planilha não estão de acordo com o padrão: '.$str_fix_headers_acab;

		if(strlen($camposDiferentes)>2):
			$_SESSION['retorno']['mensagem'] .= "<br><br><strong> Colunas não encontradas no arquivo: ".$camposDiferentes."</strong>";
		endif;

		if(strlen($camposDiferentesTabela)>2):
			$_SESSION['retorno']['mensagem'] .= "<br><br><strong> Colunas que serão ignoradas: ".$camposDiferentesTabela."</strong>";
		endif;
			
		//@TODO Aqui tem que setar as sessions de retorno
		header("location: ".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_acabamentos");
		//die();

	endif;
	
	ob_end_clean();


endif;