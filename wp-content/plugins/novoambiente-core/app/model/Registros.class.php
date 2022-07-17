<?php

	class registros extends Sql {
		
		//Indexes das tabelas, utilizados para melhorar a performance do SELECT e termos quando necessário
		private $indexes = array('id_produto' => 'gid_produto', 'id_fabrica' => 'gid_fabrica', 'id_designer' => 'gid_designer', 'id_acabamento' => 'gid_acabamento', 'id_grupo' => 'gid_grupo', 'id_tipo' => 'gid_tipo');
		
		//Define os cabeçalhos que deverão estar na planilha
		public $fix_headers_prod = array('codigo', 'nome_produto', 'descricao_produto', 'descricao_variacao', 'fabrica', 'designer', 'comprimento', 'profundidade', 'altura', 'foto1', 'foto2', 'foto3', 'foto4', 'foto5', 'novidade', 'medida_especial', 'low_cost', 'in_ecommerce', 'ecommerce_link');
		public $fix_headers_acab = array('fabrica', 'grupo_acabamento', 'tipo_acabamento', 'acabamento', 'detalhes', 'url_imagem' );
	
		//Guarda a quantidade de registros atualizados e inseridos na exportação do Xls
		public $incluidos;
		public $atualizados;

		//Determina se um produto será ou não atualizado
		public $atualizaProduto;
		
		//ID do produto a ser atualizado ou que foi inserido
		private $idProduto;
		
		//Objeto temporario para armazenar os cabeçalhos de tabela
		public $temp_headers;
		
		//Tabela que está sendo manipulada
		public $tabela;
		
		//Tradutor de campos tabela x banco de dados
		public $produtos = array('gid_produto' => 'nome_produto', 
								 'id_fabrica'  => 'fabrica');
		
		public $fabricas = array('gid_fabrica' => 'fabrica');
		
		public $designers = array('gid_designer' => 'designer');

		public $acabamentos = array('gid_acabamento' => 'acabamento');
		public $tipos_acabamentos = array('gid_tipo' => 'tipo_acabamento');
		public $grupos_acabamentos = array('gid_grupo' => 'grupo_acabamento');
								 
		public $map;								 
		
		/**
		 * getRegistros
		 * @description Retorna os registros de acordo com os parametros
		 * 
		 * @param $table(string) Tabela da qual serão recuperados os registros
		 * @param $limit(array) Dados de paginação
		 * @param $order(array) Campo para ordenação e tipo de ordenação
		 * @param $fields (string) Campos da consulta separados por virgula
		 *
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */
		public function getRegistros($table='na_sys_produtos', 
									 $limit=array('inicio' => 0, 'limite' => '100000'), 
									 $order=array('campo' => 'update_time', 'tipo' => 'DESC'),
									 $fields = '*')
		{
			//Retorna os registros de acordo com o filtro
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM '.$table.' 
					ORDER BY '.$order['campo'].' '.$order['tipo'].' 
					LIMIT '.$limit['inicio'].', '.$limit['limite'];
					
			$query = mysql_query($sql);
			
			//print $sql.'<br />'; 
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
				return $registros = array('result' => $query, 'total' => $total);
		}							 
		
		/**
		 * getActiveDesigners
		 * @description Retorna os designers ativos no sistema
		 * 
		 * @param $table(string) Tabela da qual serão recuperados os registros
		 * @param $limit(array) Dados de paginação
		 * @param $order(array) Campo para ordenação e tipo de ordenação
		 *
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */

		public function getActiveDesigners()
		{
			//Retorna os registros de acordo com o filtro
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.DB_DESIGNERS.' WHERE nome != ""
					ORDER BY nome ASC';
			
			$query = mysql_query($sql);
			
			//print $sql.'<br />'; 
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
			return $registros = array('result' => $query, 'total' => $total);
		}


		public function getProdutosFromDesigner($id, $limit=6)
		{
			$table = DB_PRODUTOS;
			//Retorna os registros de acordo com o filtro
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$table.' 
					WHERE id_designer = '.$id.'
					ORDER BY id_produto DESC
					LIMIT '.$limit;
			$query = mysql_query($sql);
			

			//print $sql.'<br />'; 
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
			return $registros = array('result' => $query, 'total' => $total);
		}



		/**
		 * getAcabamentos
		 * @description Retorna os acabamentos de acordo com os parametros
		 * 
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */
		public function getAcabamentos()
		{
			//Retorna os registros de acordo com o filtro
			$sql = "SELECT
						ac.*,
						tp.label as tipo,
						gp.label as grupo 
					FROM ".DB_ACABAMENTOS." ac,
						 ".DB_ACABAMENTOS_TIPOS." tp,
						 ".DB_ACABAMENTOS_GRUPOS." gp
					WHERE
						ac.id_grupo = gp.id_grupo AND
						ac.id_tipo = tp.id_tipo";
			$query = mysql_query($sql);
			
			//echo $sql.'<br />';
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
				return $registros = array('result' => $query, 'total' => $total);
		}

		
		/**
		 * getAcabamentosGruposByTipo
		 * @description Retorna os acabamentos e seu grupo de acordo com o id do tipo e id da fabrica
		 * 
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */
		public function getAcabamentosGruposByTipo($id_tipo, $id_fabrica)
		{
			//Retorna os registros de acordo com o filtro
			$sql = "SELECT
						ac.*,
						tp.label as tipo,
						gp.label as grupo 
					FROM ".DB_ACABAMENTOS." ac,
						 ".DB_ACABAMENTOS_TIPOS." tp,
						 ".DB_ACABAMENTOS_GRUPOS." gp
					WHERE
						ac.id_grupo = gp.id_grupo AND
						ac.id_tipo = ".$id_tipo." AND
						ac.id_fabrica = ".$id_fabrica;
						
			$query = mysql_query($sql);
			
			//echo $sql.'<br />';
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
				return $registros = array('result' => $query, 'total' => $total);
		}

		/**
		 * getTiposAcabamentos
		 * @description Retorna os acabamentos de acordo com os parametros
		 * 		 
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */
		public function getTiposAcabamentos()
		{
			//Retorna os registros de acordo com o filtro
			$sql = "SELECT
						tp.*,
						gp.label as grupo 
					FROM ".DB_ACABAMENTOS_TIPOS." tp,
						 ".DB_ACABAMENTOS_GRUPOS." gp
					WHERE
						tp.id_grupo = gp.id_grupo";
			$query = mysql_query($sql);
			
			//echo $sql.'<br />';
			//Verifica o total de registros para paginação
			$sql   = "SELECT FOUND_ROWS() AS `found_rows`;";
			$rows  = mysql_query($sql);
			$rows  = mysql_fetch_assoc($rows);
			$total = $rows['found_rows'];
			
				return $registros = array('result' => $query, 'total' => $total);
		}


		/**
		 * getValores
		 * @description Retorna os valores de tipos de acabamentos X variação do produto
		 * 
		 * @param $table(string) Tabela da qual serão recuperados os registros
		 * @param $limit(array) Dados de paginação
		 * @param $order(array) Campo para ordenação e tipo de ordenação
		 *
		 * @return $registros(array) result => registros em formato SQL, 'total' => total de registros para paginação
		 */
		public function getValores($idsProdutos)
		{
			//Retorna os registros de acordo com o filtro
			$sql = "SELECT
						valores.*,
						produtos.label as NOME_PRODUTO,
						produtos.id_produto as IDPRODUTO
					FROM ".DB_PRODUTOS." produtos,
						 ".DB_VALORES." valores,
						 ".DB_VARIACOES." variacoes
					WHERE
						valores.id_variacao = variacoes.id_variacao AND
						variacoes.id_produto = produtos.id_produto AND
						produtos.id_produto IN ('".$idsProdutos."') ";
			$query = mysql_query($sql);
			
			// Monta o array para print dos dados em tabela
			$valores = array();
			while ($objValores = mysql_fetch_object($query)):
				$valores[$objValores->id_variacao][$objValores->id_acabamento_tipo] = array( 'valor' => $objValores->valor, 'id' => $objValores->id_valor);
			endwhile;
			
				return $valores;

		}
				

		/**
		 * getRegistro
		 * @description Retorna informações de um produto e suas variações
		 * 
		 * @param $table(string) Tabela da qual será retornado o produto
		 * @param $label(array) Campo para identificação do produto
		 * @param $value(array) Valor do campo para condição where
		 *
		 * @return $result(array) 	value => 1 para sucesso, 0 para erro
		 *							registro => informações do produto(objeto) OU detalhe do erro
		 */
		public function getRegistro($table='na_sys_produtos', $label, $value)
		{
			if(!empty($label)&&!empty($value)):
				//Retorna o registro de acordo com o label e valor informado
				$sql = 'SELECT * FROM '.$table.' 
						WHERE '.$label.' = '.$value;
				$query = mysql_query($sql);

				if (!$query):

					error_log('Erro na consulta ao banco de dados. Em Produtos -> Detalhe. Mysql error:' . mysql_error());
				    return $result = array('value' => 0, 'registro' => 'Erro de acesso. Por favor, entre em contato com o administrador.');
					
				elseif (mysql_num_rows($query) == 0):
				    
				    return $result = array('value' => 0, 'registro' => 'Produto não encontrado.');
				
				elseif (mysql_num_rows($query) < 1):

					return $result = array('value' => 0, 'registro' => 'Produto corrompido.  Por favor, entre em contato com o administrador.');
				
				else:

					$row = mysql_fetch_object($query);
					return $result = array('value' => 1, 'registro' => $row);

				endif;

			else:
				
				return $result = array('value' => 0, 'registro' => 'Produto não encontrado. Parâmetros vazios. Por favor, entre em contato com o administrador.');
			
			endif;
		}
		
		/**
		 * getArrayFromXLS
		 * @description Recupera um array com os dados dos registros a partir de um arquivo XLS
		 * 
		 * @param $xls(file) Caminho completo para o arquivo no formato XLS
		 *
		 * @return $tabela(array) Produtos em formato array, cada chave contem uma linha com os dados do produto
		 */
		public function getArrayFromXLS($xls)
		{
			$data = new Spreadsheet_Excel_Reader($xls, false, 'ISO-8859-1');
            //$data = new Spreadsheet_Excel_Reader($xls, false, 'UTF-8');
			//$data->setOutputEncoding('UTF-8');	
			//Recupera os dados que necessitamos, ou seja, os dados da tabela
			$tabela = $data->sheets[0];
            //echo '<pre>'; echo 'Dados que vem da LIB <br />';
			//print_r($tabela);
            
			//Armazena os headers para que o metodo atualizaLista possa saber quais campos serão atualizados
			$this->temp_headers = $tabela['cells'][1];
			unset($tabela['cells'][1]);
			
			if(!isset($tabela['cells'][1])):
				return $tabela['cells'];
			else:
				return false;
			endif;
		}

		/**
	     * setValores
	     * @description Cadastra uma nova oferta de compra no sistema
	     * 
	     * @param $arrValores(array) Valores a serem inseridos
	     * 
	     * @return boolean
	     */
	    public function setValores($arrValores)
	    {	
	    	//print "<pre>";
	        //print_r($arrValores); die;
	        $erro=0;
	    	$idFabrica = $arrValores['fabrica'];
	    	$idProduto = $arrValores['produto'];
	        unset($arrValores['save']);
	        unset($arrValores['fabrica']);
	        unset($arrValores['produto']);

	        if(!empty($idFabrica)&&!empty($idProduto)):

		        foreach($arrValores as $key => $value):

		        	// Verifica se a chave possui '-' que identificará se a ação é update ou insert
					$posicaoStr = stripos($key, '-');

		        	if ($posicaoStr !== false) {
						// insert
						if(!empty($value)):
							$variacao_tipo = explode('-', $key);

							$sql = "INSERT INTO ".DB_VALORES." 
										( id_valor, id_variacao, id_acabamento_tipo, valor ) VALUES
										( 0, ".$variacao_tipo[0].", ".$variacao_tipo[1].", ".$value.")";
							$query = mysql_query($sql);
							/*print $sql."<BR>";
							print "<hr>";*/

						endif;
					
					} else {
						// update
						$this->tabela = DB_VALORES;
			            $this->index = 'id_valor';
			            $arrUpdate = array('valor' => $value);
						$this->set($key, $arrUpdate);
					}

		        endforeach;

			        $_SESSION["globalRedirectUrl"] = "wp-admin/admin.php?page=novo_ambiente_fabricas&view=editar&fab=".$idFabrica."&value=".$idProduto;
					$_SESSION['retorno']['classe'] = 'sucesso';
		            $_SESSION['retorno']['mensagem'] = 'Valores editados com sucesso.';
		        
		   	else:

	        	$_SESSION["globalRedirectUrl"] = "wp-admin/admin.php?page=novo_ambiente_fabricas&view=produtos&value=1";
				$_SESSION['retorno']['classe'] = 'erro';
	            $_SESSION['retorno']['mensagem'] = 'Erro interno. Por favor, contate o administrador.';
			
			endif;
			//die;
	    }
		
		/**
		 * atualizaBase
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $data(array) Dados para insert ou update
		 * @param $field_check(string) Campo que será verificado para evitar duplicidade
		 * @param $key_check(string) Especifica em qual chave do array está o campo que não pode ser duplicado
		 * @param $metodo(string) Metodo que deve ser chamado após verificação de dados duplicados
		 *
		 * @return boolean
		 */
		public function atualizaBase($data, $field_check, $key_check='', $metodo, $type='normal')
		{ 
		
			$operator  = is_string($field_check) ? 'like' : '=';
			$concatena = is_string($field_check) ? "'" : '';
						
			if(empty($key_check))
				$key_check = $field_check;
				
			//Só verificamos a existência do registro, se o mesmo estiver setado no array principal, a classe que le o Excel remove algumas celulas vazias, cuidado!
			if(array_key_exists($key_check, $data)):
				
				if (substr($data[$key_check], 0, 2) == 'am'):
					
				endif;
				$index = array_search($field_check, $this->indexes);
				
				if($type=='acabamentos'):
					$sql_check = 'SELECT '.$field_check.', '.$index.' FROM '.$this->tabela.' WHERE id_fabrica = '.$this->map['produtos']['id_fabrica']." AND id_tipo = ".$this->map['acabamentos']['id_tipo']." AND id_grupo = ".$this->map['acabamentos']['id_grupo']." AND gid_acabamento = ".$concatena.$this->makeSlug(utf8_encode($data[$key_check])).$concatena;
				else:
					$sql_check = 'SELECT '.$field_check.', '.$index.' FROM '.$this->tabela.' WHERE '.$field_check.' '.$operator.' '.$concatena.$this->makeSlug(utf8_encode($data[$key_check])).$concatena;
				endif;

				$query = mysql_query($sql_check);

				//print $sql_check."<hr>"; 
				if(mysql_num_rows($query) > 0):
					$obj = mysql_fetch_object($query);
					$this->$metodo('update', $data, $obj->$index);
					$this->atualizados['total']++;

					$result['resultado'] = true;
					$result['consulta'] = 'update';

					return $result;

				else:
					$this->$metodo('insert', $data);
					$this->incluidos['total']++;

					$result['resultado'] = true;
					$result['consulta'] = 'insert';

					return $result;

				endif;
				
				
			else:
				
				$result['resultado'] = false;
				$result['consulta'] = '';

				return $result;

			endif;
		}

		
		/**
		 * atualizaAcabamentos
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(ID) Se a operação for um update, deve ser passado o ID que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaAcabamentos($operator, $data, $id=0)
		{
			$sql = '';
			
			switch($operator):
				
				case 'insert':
					$sql = "INSERT INTO ".$this->tabela." 
							VALUES (0, ".$this->map['produtos']['id_fabrica'].", ".$this->map['acabamentos']['id_tipo'].", ".$this->map['acabamentos']['id_grupo'].", 0, '".utf8_encode($data[$this->map['acabamentos']['label']])."', '".$this->makeSlug(utf8_encode($data[$this->map['acabamentos']['gid']]))."', '".utf8_encode($data[$this->map['acabamentos']['detalhes']])."', '".utf8_encode($data[$this->map['acabamentos']['url_imagem']])."', 'draft')";
					
					$arrAtual = array('nome' => $data[$this->map['acabamentos']['label']], 'tipo' => $this->map['acabamentos']['id_tipo'], 'grupo' => $this->map['acabamentos']['id_grupo']);
					
					if(!in_array($arrAtual, $this->incluidos['acabamentos'])):
						array_push($this->incluidos['acabamentos'], $arrAtual);
					endif;

					break;
					
				case 'update':
					
					$arrAtual = array('nome' => $data[$this->map['acabamentos']['label']], 'tipo' => $this->map['acabamentos']['id_tipo'], 'grupo' => $this->map['acabamentos']['id_grupo']);
					
					if(!in_array($arrAtual, $this->atualizados['acabamentos'])):
						array_push($this->atualizados['acabamentos'], $arrAtual);
					endif;

					/*
					$sql = "UPDATE ".$this->tabela." SET id_fabrica  = ".$this->map['produtos']['id_fabrica'].", 
															 id_designer = ".$this->map['produtos']['id_designer'].",
															 cod_produto = '".utf8_encode($data[$this->map['produtos']['cod_produto']])."', 
															 label = '".utf8_encode($data[$this->map['produtos']['label']])."', 
															 gid_produto = '".$this->makeSlug(utf8_encode($data[$this->map['produtos']['label']]))."'
															 WHERE id_produto = ".$id;
					*/
					break;
					
			endswitch;
			
			if(!empty($sql)):
				try{
					mysql_query($sql);
				}catch(Exception $e){
					error_log($e->getMessage(), 0);
				}
				
				if($id == 0)
					$id = mysql_insert_id();
				return true;
			else:
				return false;
			endif;
		}

		/**
		 * atualizaTiposAcabamentos
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(ID) Se a operação for um update, deve ser passado o ID que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaTiposAcabamentos($operator, $data, $id=0)
		{
			$sql = '';
			$this->map['acabamentos']['id_tipo'] = $id;

			switch($operator):
				
				case 'insert':
					$sql = "INSERT INTO ".$this->tabela." 
							VALUES (0, '".$this->map['acabamentos']['id_grupo']."', '".utf8_encode($data[$this->map['tipos_acabamentos']['label']])."', '".$this->makeSlug(utf8_encode($data[$this->map['tipos_acabamentos']['label']]))."')";
					
					if (!in_array($data[$this->map['tipos_acabamentos']['label']], $this->incluidos['tipos'])): 
						array_push($this->incluidos['tipos'], $data[$this->map['tipos_acabamentos']['label']]);
					endif;

					break;
					
				case 'update':

					if (!in_array($data[$this->map['tipos_acabamentos']['label']], $this->atualizados['tipos'])): 
						array_push($this->atualizados['tipos'], $data[$this->map['tipos_acabamentos']['label']]);
					endif;

					break;
					
			endswitch;
			
			if(!empty($sql)):
				try{
					mysql_query($sql);
				}catch(Exception $e){
					error_log($e->getMessage(), 0);
				}
				
				if($id == 0)
					$id = mysql_insert_id();
				$this->map['acabamentos']['id_tipo'] = $id;
				return true;
			else:
				return false;
			endif;
		}

		/**
		 * atualizaGruposAcabamentos
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(ID) Se a operação for um update, deve ser passado o ID que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaGruposAcabamentos($operator, $data, $id=0)
		{
			$sql = '';
			$this->map['acabamentos']['id_grupo'] = $id;
		
			switch($operator):
				
				case 'insert':

					$sql = "INSERT INTO ".$this->tabela." 
							VALUES (0, '".utf8_encode($data[$this->map['grupos_acabamentos']['label']])."', '".$this->makeSlug(utf8_encode($data[$this->map['grupos_acabamentos']['gid']]))."' )";
					
					if (!in_array($data[$this->map['grupos_acabamentos']['label']], $this->incluidos['grupos'])): 
						array_push($this->incluidos['grupos'], $data[$this->map['grupos_acabamentos']['label']]);
					endif;

					break;
					
				case 'update':
					
					if (!in_array($data[$this->map['grupos_acabamentos']['label']], $this->atualizados['grupos'])): 
						array_push($this->atualizados['grupos'], $data[$this->map['grupos_acabamentos']['label']]);
					endif;

					break;
					
			endswitch;
			
			if(!empty($sql)):
				try{
					mysql_query($sql);
				}catch(Exception $e){
					error_log($e->getMessage(), 0);
				}
				
				if($id == 0)
					$id = mysql_insert_id();
				$this->map['acabamentos']['id_grupo'] = $id;
				return true;
			else:
				return false;
			endif;
		}         

		/**
		 * atualizaFabrica
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(ID) Se a operação for um update, deve ser passado o ID que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaFabrica($operator, $data, $id=0)
		{
			$sql = '';
			$this->map['produtos']['id_fabrica'] = $id;
			switch($operator):
				
				case 'insert':

					$sql = "INSERT INTO ".$this->tabela." 
							VALUES (0, '".utf8_encode($data[$this->map['fabrica']['label']])."', '".$this->makeSlug(utf8_encode($data[$this->map['fabrica']['label']]))."', 'draft')";
					
					if (!in_array($data[$this->map['fabrica']['label']], $this->incluidos['fabricas'])): 
						array_push($this->incluidos['fabricas'], $data[$this->map['fabrica']['label']]);
					endif;

					break;
				
				case 'update':
					
					if (!in_array($data[$this->map['fabrica']['label']], $this->atualizados['fabricas'])): 
						array_push($this->atualizados['fabricas'], $data[$this->map['fabrica']['label']]);
					endif;

					break;
					
			endswitch;
			
			if(!empty($sql)):
				try{
					mysql_query($sql);
				}catch(Exception $e){
					error_log($e->getMessage(), 0);
				}
				
				if($id == 0)
					$id = mysql_insert_id();
				$this->map['produtos']['id_fabrica'] = $id;
				return true;
			else:
				return false;
			endif;
		}
		
		/**
		 * atualizaDesigner
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(int) ID do item que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaDesigner($operator, $data, $id=0)
		{ 
			$sql = '';
			$this->map['produtos']['id_designer'] = $id;
			switch($operator):
				
				case 'insert':

					$sql = "INSERT INTO ".$this->tabela." 
							VALUES (0, 0, '".utf8_encode($data[$this->map['designer']['nome']])."', '".$this->makeSlug(utf8_encode($data[$this->map['designer']['nome']]))."', 'draft')";
					if (!in_array($this->makeSlug($data[$this->map['designer']['nome']]), $this->incluidos['designers'])): 
						array_push($this->incluidos['designers'], $this->makeSlug($data[$this->map['designer']['nome']]));
					endif;

					break;
				
				case 'update':
					
					if (!in_array($this->makeSlug($data[$this->map['designer']['nome']]), $this->atualizados['designers'])): 
						array_push($this->atualizados['designers'], $this->makeSlug($data[$this->map['designer']['nome']]));
					endif;
					
					break;
					
			endswitch;
			
			if(!empty($sql)):
				$query = mysql_query($sql);

				if($query):
					$id = mysql_insert_id();
					$this->map['produtos']['id_designer'] = $id;
					return true;
				else:
					$this->map['produtos']['id_designer'] = 0;
				endif;
			else:
				if($operator=='update'): $this->map['produtos']['id_designer'] = $id; endif;
				return false;
			endif;
		}
		
		/**
		 * atualizaProduto
		 * @description Atualiza a base dados, inserindo ou realizando update
		 * 
		 * @param $operator(string) Operação que está sendo efetuada
		 * @param $data(array) Array com informações de insert ou update
		 * @param $id(int) ID do item que está sendo atualizado
		 *
		 * @return boolean
		 */
		private function atualizaProduto($operator, $data, $id=0)

		{ //echo '<pre>'; print_r($data);

			$sql = '';

			$this->atualizaProduto = true;
			
			if(empty($data[1])):

				$sqlNa = "SELECT cod_produto FROM ".DB_PRODUTOS." WHERE cod_produto like 'na%' ORDER BY cod_produto DESC LIMIT 1";
				$query = mysql_query($sqlNa);
				
				if(mysql_num_rows($query) > 0):
					$obj = mysql_fetch_object($query);
					$numNa = str_replace("", "na", $obj->cod_produto);
					$numNa++;
					$data[1] = $numNa;				
				else:
					$data[1] = 'na00001';
				endif;
			endif;

			$fabrica = (isset($this->map['produtos']['id_fabrica'])&&!empty($this->map['produtos']['id_fabrica']))?$this->map['produtos']['id_fabrica']:0;
			$designer = (isset($this->map['produtos']['id_designer'])&&!empty($this->map['produtos']['id_designer']))?$this->map['produtos']['id_designer']:0;
			$cod_produto = (isset($data[$this->map['produtos']['cod_produto']])&&!empty($data[$this->map['produtos']['cod_produto']]))?utf8_encode($data[$this->map['produtos']['cod_produto']]):'';
			$prod_label = (isset($data[$this->map['produtos']['label']])&&!empty($data[$this->map['produtos']['label']]))?utf8_encode($data[$this->map['produtos']['label']]):'';
			$prod_gid = (isset($data[$this->map['produtos']['label']])&&!empty($data[$this->map['produtos']['label']]))?$this->makeSlug(utf8_encode($data[$this->map['produtos']['label']])):'';
			$prod_descricao = (isset($data[$this->map['produtos']['descricao']])&&!empty($data[$this->map['produtos']['descricao']]))?utf8_encode($data[$this->map['produtos']['descricao']]):'';
			$prod_novidade = (isset($data[$this->map['variacoes']['novidade']])&&!empty($data[$this->map['variacoes']['novidade']]))?$data[$this->map['variacoes']['novidade']]:'';
			$prodMedida_especial = (isset($data[$this->map['variacoes']['medida_especial']])&&!empty($data[$this->map['variacoes']['medida_especial']]))?$data[$this->map['variacoes']['medida_especial']]:'';
			$prodLow_cost = (isset($data[$this->map['variacoes']['low_cost']])&&!empty($data[$this->map['variacoes']['low_cost']]))?$data[$this->map['variacoes']['low_cost']]:'';
			$prodIn_ecommerce = (isset($data[$this->map['variacoes']['in_ecommerce']])&&!empty($data[$this->map['variacoes']['in_ecommerce']]))?$data[$this->map['variacoes']['in_ecommerce']]:'';
			$prodEcommerce_link = (isset($data[$this->map['variacoes']['ecommerce_link']])&&!empty($data[$this->map['variacoes']['ecommerce_link']]))?$data[$this->map['variacoes']['ecommerce_link']]:'';

			switch($operator):

				case 'insert':

					$sql = "INSERT INTO ".$this->tabela." (id_produto, id_fabrica, id_designer, cod_produto, label, gid_produto, descricao, novidade, medida_especial, low_cost, in_ecommerce, ecommerce_link)
							VALUES (0,  ".$fabrica.", ".$designer.", '".$cod_produto."', '".$prod_label."', '".$prod_gid."', '".$prod_descricao."', '".$prod_novidade ."', '".$prodMedida_especial."', '".$prodLow_cost."', '".$prodIn_ecommerce."', '".$prodEcommerce_link."')";
                            //echo $sql; die();
			
			         $arrAtual = array('nome' => $data[$this->map['produtos']['label']], 'codigo' => $this->map['produtos']['cod_produto'], 'gid' => $this->makeSlug(utf8_encode($data[$this->map['produtos']['label']])));
				
					if(!in_array($arrAtual, $this->incluidos['produtos'])):
						array_push($this->incluidos['produtos'], $arrAtual);
					endif;
					
					break;
				
				case 'update':

					$set_designer = (empty($designer)) ? '' : 'id_designer = '.$designer.',';
					$set_fabrica = (empty($fabrica)) ? '' : 'id_fabrica = '.$fabrica.',';

					$sql = "UPDATE ".$this->tabela." SET 	$set_fabrica
													$set_designer
													cod_produto = '".$cod_produto."', 
													label = '".$prod_label."', 
													gid_produto = '".$prod_gid."',
													descricao = '".$prod_descricao."',
													novidade = '".$prod_novidade."',
													medida_especial = '".$prodMedida_especial."',
													low_cost = '".$prodLow_cost."',
													in_ecommerce = '".$prodIn_ecommerce."',
													ecommerce_link = '".$prodEcommerce_link."'

												WHERE id_produto = ".$id;

					$arrAtual = array('nome' => $data[$this->map['produtos']['label']], 'codigo' => $this->map['produtos']['cod_produto'], 'gid' => $this->makeSlug(utf8_encode($data[$this->map['produtos']['label']])));
					
					if(!in_array($arrAtual, $this->atualizados['produtos'])):
						array_push($this->atualizados['produtos'], $arrAtual);
					endif;
				
					break;
			endswitch;

			if(!empty($sql)):

				$query = mysql_query($sql);

				if($id == 0):
					$this->idProduto = mysql_insert_id();
				else:
					$this->idProduto = $id;
				endif;
				return true;
			else:
				return false;
			endif;

				return true;

		}
		
		/**
		 * atualizaDetalhesProduto
		 * @description Atualiza os detalhes do produto baseado nas dimensões
		 * 
		 * @param $data(array) Detalhes do produto
		 * @param $this->idProduto(object) Objeto gerado pelo metodo atualizaProduto
		 *
		 * @return boolean
		 */
		public function atualizaDetalhesProduto($data)
		{	
			
			$altura = (isset($data[$this->map['variacoes']['altura']])&&!empty($data[$this->map['variacoes']['altura']]))?$this->makeSlug($data[$this->map['variacoes']['altura']]):0;
			$comprimento = (isset($data[$this->map['variacoes']['comprimento']])&&!empty($data[$this->map['variacoes']['comprimento']]))?$this->makeSlug($data[$this->map['variacoes']['comprimento']]):0;
			$profundidade = (isset($data[$this->map['variacoes']['profundidade']])&&!empty($data[$this->map['variacoes']['profundidade']]))?$this->makeSlug($data[$this->map['variacoes']['profundidade']]):0;
			$gid_descricao = (isset($data[$this->map['variacoes']['descricao']])&&!empty($data[$this->map['variacoes']['descricao']]))?$this->makeSlug(utf8_encode($data[$this->map['variacoes']['descricao']])):'';
			$foto1 = (isset($data[$this->map['variacoes']['foto1']])&&!empty($data[$this->map['variacoes']['foto1']]))?utf8_encode($data[$this->map['variacoes']['foto1']]):'';
			$foto2 = (isset($data[$this->map['variacoes']['foto2']])&&!empty($data[$this->map['variacoes']['foto2']]))?utf8_encode($data[$this->map['variacoes']['foto2']]):'';
			$foto3 = (isset($data[$this->map['variacoes']['foto3']])&&!empty($data[$this->map['variacoes']['foto3']]))?utf8_encode($data[$this->map['variacoes']['foto3']]):'';
			$foto4 = (isset($data[$this->map['variacoes']['foto4']])&&!empty($data[$this->map['variacoes']['foto4']]))?utf8_encode($data[$this->map['variacoes']['foto4']]):'';
			$foto5 = (isset($data[$this->map['variacoes']['foto5']])&&!empty($data[$this->map['variacoes']['foto5']]))?utf8_encode($data[$this->map['variacoes']['foto5']]):'';
			$descricao = (isset($data[$this->map['variacoes']['descricao']])&&!empty($data[$this->map['variacoes']['descricao']]))?utf8_encode($data[$this->map['variacoes']['descricao']]):'';
			$gid_descricao = (isset($data[$this->map['variacoes']['descricao']])&&!empty($data[$this->map['variacoes']['descricao']]))?$this->makeSlug(utf8_encode($data[$this->map['variacoes']['descricao']])):'';

			$sql_check = "SELECT id_variacao FROM ".$this->tabela." WHERE  
															altura like '".$altura."' AND 
															comprimento like '".$comprimento."' AND 
															profundidade like '".$profundidade."' AND
															gid_descricao like '".$gid_descricao."' AND
															id_produto = ".$this->idProduto; 
		
			$query_check = mysql_query($sql_check);

			if($query_check&&mysql_num_rows($query_check) > 0):

				$obj = mysql_fetch_object($query_check);

				$arrSql = array(	'altura' => $altura,
								'comprimento' => $comprimento,
								'profundidade' => $profundidade,
								'foto1' => $foto1,
								'foto2' => $foto2,
								'foto3' => $foto3,
								'foto4' => $foto4,
								'foto5' => $foto5,
								'descricao' => $descricao,
								'gid_descricao' => $gid_descricao );

				$this->tabela = DB_VARIACOES;
				$this->index = 'id_variacao';
				$identificador = $obj->id_variacao;

				$query = $this->set($identificador, $arrSql);

				$this->atualizados['produtos_variacoes']++;

				$arrAtual = array('produto' => $this->idProduto, 'altura' => $altura, 'comprimento' => $comprimento, 'profundidade' => $profundidade, 'descricao' => $descricao);
					
				if(!in_array($arrAtual, $this->atualizados['variacoes'])):
					array_push($this->atualizados['variacoes'], $arrAtual);
					$this->atualizados['total']++;
				endif;

				$result['consulta'] = 'update';
				$result['resultado'] = ($query)?true:false;

			else:

				$sql = "INSERT INTO ".$this->tabela." VALUES(0, ".$this->idProduto.", 
																".$altura.",
																".$comprimento.",
																".$profundidade.",
																'".$descricao."',
																'".$gid_descricao."',
																'".$foto1."',
																'".$foto2."',
																'".$foto3."',
																'".$foto4."',
																'".$foto5."',
																'draft') ";
				
				$arrAtual = array('produto' => $this->idProduto, 'altura' => $altura, 'comprimento' => $comprimento, 'profundidade' => $profundidade, 'foto1' => $foto1);
					
				if(!in_array($arrAtual, $this->incluidos['variacoes'])):
					array_push($this->incluidos['variacoes'], $arrAtual);
					$this->incluidos['total']++;
				endif;

				$result['consulta'] = 'insert';

				$query = mysql_query($sql);
				$result['resultado'] = ($query)?true:false;

			endif;

				return $result;

		}


		/**
		 * criaPdf
		 * @description Cria pdf com informações de produtos
		 * 
		 * @param $inseridos(int) Valor das importações inseridas
		 * @param $atualizados(int) Valor das importações atualizadas
		 * @param $tipo(string) Nome do registro importado
		 *
		 * @return boolean
		 */
		public function criaPdf($idsProdutos, $template, $headerTitle)
		{
			ob_start();
			error_reporting(0);
			
			switch($template):
				case 'produto': $headerTitle = 'Produto '.$objProduto->label; $imageType = 'foto-galeria'; break;
				case 'categoria': $imageType = 'thumb-home'; break;
				case 'minha-lista': $imageType = 'mini'; break;
			endswitch;
			
			//Dados dos produtos
			//$sqlProduto = "SELECT * FROM ".DB_PRODUTOS." WHERE id_post IN ('".$idsProdutos."')";
			//@update 12-03-2014 Nascimento Removi os campos não necessários nesta query
			$sqlProduto = "SELECT id_produto, id_designer, id_fabrica, id_post, descricao, label, cod_produto FROM ".DB_PRODUTOS." WHERE id_post IN ('".$idsProdutos."')";

			$qProduto = mysql_query($sqlProduto);

			$i = 0;
			$html = array();
			$produtos = array();

			while($objProduto = mysql_fetch_object($qProduto)):

				/** $objDesigner = (isset($objProduto->id_designer))
					? $this->getRegistro(DB_DESIGNERS, 'id_designer', $objProduto->id_designer)
					: 0; */
					
					//@update 12-03-2014 Atualizando o SQL, aqui usamos apenas o nome
					$objDesigner = (isset($objProduto->id_designer))
					? $this->getRegistros(	DB_DESIGNERS." WHERE id_designer = ".$objProduto->id_designer,
											array('inicio' => 0, 'limite' => '100000'), 
	               							array('campo' => 'id_designer', 'tipo' => 'ASC'),
											'nome')
					: 0;
					
					//@update Nascimento 12-03-2014 Acertei o resultado para atender a lógica do template, mas isso pode melhorar
					if(isset($objDesigner['total']) && $objDesigner['total'] > 0){
						$objDesigner = array('value' => 1, 'registro' => mysql_fetch_object($objDesigner['result']));
					}

	               $produtos[$i]["info"] = $objProduto;
	               $produtos[$i]["designer"] = $objDesigner;
	               $produtos[$i]['imagem'] = (get_the_post_thumbnail($objProduto->id_post, $imageType))
					? get_the_post_thumbnail($objProduto->id_post, $imageType)
					: get_bloginfo('template_url').'/img/no-image-thumb.jpg';
					//$produtos[$i]['imagem'] = get_bloginfo('template_url').'/img/no-image-thumb.jpg';
								
	
				$arrTiposAcabamentos = array();
	               $arrGruposAcabamentos = array();
				   //@update 12-04-2014 Nascimento Alterei o método getRegistro, acrecentando um último parametro com campos da consulta
	               $consultaVariacoes = $this->getRegistros(DB_VARIACOES." WHERE id_produto = ".$objProduto->id_produto, 
	               											array('inicio' => 0, 'limite' => '100000'), 
	               											array('campo' => 'id_variacao', 'tipo' => 'DESC'),
															'altura, comprimento, profundidade, descricao');
	                                    
	               if($consultaVariacoes['total']>0):
	                   	$m = 0;
                        	while($objVariacoes = mysql_fetch_object($consultaVariacoes['result'])):

                        		$produtos[$i]["variacoes"][$m] = $objVariacoes;
                            	$m++;
                        	endwhile;
	               endif;

      			$sqlGruposAcabamentos = "SELECT grupos.* 
                             FROM    na_sys_acabamentos_grupos as grupos,
                                     na_sys_acabamentos as acabamentos,
                                     na_sys_acabamentos_tipos as tipos,
                                     na_sys_produtos_variacoes as variacoes,
                                     na_sys_valores as valores
                             WHERE   acabamentos.id_fabrica = ".$objProduto->id_fabrica." AND
                                     acabamentos.id_grupo = grupos.id_grupo AND
                                     acabamentos.id_tipo =  tipos.id_tipo AND
                                     valores.id_variacao = variacoes.id_variacao AND
                                     valores.id_acabamento_tipo = tipos.id_tipo AND
                                     variacoes.id_produto = ".$objProduto->id_produto." AND
                                     valores.valor <> 0
                                  GROUP BY grupos.label";

                    $consultaGruposAcabamentos = mysql_query($sqlGruposAcabamentos);

              		if($consultaGruposAcabamentos&&mysql_num_rows($consultaGruposAcabamentos)>0):

                  		while($objGruposAcabamentos = mysql_fetch_object($consultaGruposAcabamentos)): 

                             	$sqlAcabamentos = "SELECT    acabamentos.*,
                                                          acabamentos.label as label_acabamento,
                                                          tipos.*
                                                  FROM    na_sys_acabamentos_grupos as grupos,
                                                          na_sys_acabamentos as acabamentos,
                                                          na_sys_acabamentos_tipos as tipos,
                                                          na_sys_produtos_variacoes as variacoes,
                                                          na_sys_valores as valores
                                                  WHERE   acabamentos.id_fabrica = ".$objProduto->id_fabrica." AND
                                                          acabamentos.id_grupo = grupos.id_grupo AND
                                                          acabamentos.id_tipo =  tipos.id_tipo AND
                                                          valores.id_variacao = variacoes.id_variacao AND
                                                          valores.id_acabamento_tipo = tipos.id_tipo AND
                                                          variacoes.id_produto = ".$objProduto->id_produto." AND
                                                          valores.valor <> 0 AND
                                                          grupos.id_grupo = ".$objGruposAcabamentos->id_grupo."
                                                  GROUP BY acabamentos.label
                                                  ORDER BY tipos.label";

                            	$consultaAcabamentos = mysql_query($sqlAcabamentos);
					
						if($consultaAcabamentos):
                        			$m = 0;
                         		while($objAcabamentos = mysql_fetch_array($consultaAcabamentos)):
		                  			$produtos[$i]["acabamentos"][$objGruposAcabamentos->label][$m] = $objAcabamentos;
		                  			$m++;
                                 	endwhile;
                             	else:
                                   //print $sqlAcabamentos;
                             	endif;

                         endwhile;

                    endif;

			   	$i++;

				$header_string = ($template == 'produto') ? get_permalink($objProduto->id_post) : PDF_HEADER_STRING;
				$header_title = ($template == 'produto') ? PDF_HEADER_TITLE.' - '.get_the_title($objProduto->id_post) : PDF_HEADER_TITLE.' - '.$headerTitle;

        		endwhile;

			$table_style = 'style="border: solid 1px #e0e4e6; padding: 10px;"';	//Table style
			$filename ="novo-ambiente-especificacoes-".date("dmy-Hi").".pdf"; //O nome com que o arquivo vai ser salvo, já com a extensão

			$orientationPage = ($template=='produto')?'l':'p';

			$pdf = new TCPDF($orientationPage, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		

			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Novo Ambiente');
			$pdf->SetTitle('Relatório produtos');
			$pdf->SetSubject('Relatório produtos - Novo Ambiente');
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $header_title, $header_string);
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			$pdf->SetFont('helvetica', '', 10);
			$pdf->AddPage();
		
		
			$style = "<style>
						table { font-size: 12px; font-family: arial, helvetica, sans-serif; }
						table strong { color: #A0102B; }
						table td img { width: 200px; height: auto; }
					  </style>";
		
			$pdf->writeHTML($style, true, false, true, false, '');

			$loopTemplate = 0;
			foreach($produtos as $key => $value):
				
				//print_r($value['info']); die();
				
				if (($template=='categoria') && ($loopTemplate%4==0) && ($loopTemplate > 2)){ $pdf->AddPage(); }
				
				$html = $this->getProdutoTemplate($value, $template);
				$pdf->writeHTML($html, true, false, true, false, '');

				// Quebra de página
				//if ($template=='produto'){ $pdf->AddPage(); }
				
				$loopTemplate++;
			endforeach;

			if(isset($_COOKIE['uid'])):
				$sqlUsuario = "SELECT * FROM ".DB_USUARIOS." WHERE id = ".$_COOKIE['uid'];
				$queryUsuario = mysql_query($sqlUsuario);
				$objUsuario = mysql_fetch_object($queryUsuario);

				if(isset($objUsuario->id)):
					$log = array(
						'id_usuario' => $objUsuario->id,
						'mensagem' => "Usuário ".$objUsuario->email." baixou pdf com produtos: '".$idsProdutos."'"
					);
					$this->registrar($log, __METHOD__);
					//$this->enviarEmail($objUsuario->email, "PDF - Catálogo Novo Ambiente", "Bem vindo ao Novo Ambiente", "Seu cadastro foi realizado com sucesso no cat&aacute;logo online do Novo Ambiente");

				endif;
			endif;

			$pdf->lastPage();
			$pdf->Output($filename, 'D');
				
		}


		private function getProdutoTemplate($produto, $template = 'produto'){

			$cod_produto = (strlen($produto['info']->cod_produto) >0) ? 'cod '.$produto['info']->cod_produto : 'Código a confirmar';
			switch ($template):
				case "produto":
					
					$designer = (!empty($produto['designer']['registro']->nome))?$produto['designer']['registro']->nome:'';
					$descricao = (strlen($produto['info']->descricao) > 600) ? substr($produto['info']->descricao, 0, 600). "[..]" : $produto['info']->descricao;
					
					list($width, $height, $type, $attr) = urlencode($produto['imagem']);

					if ($height > $width):
						$width = round($width/$height*500);
						$height = "500";
					endif;
						
					$html = 	'<table border="0" width="100%">
								<tr>
									<td width="600" height="20"></td>
									<td rowspan="6" style="text-align:right"><img src="'.$produto['imagem'].'" style="max-height: 400px !important; " width="'.$width.'" height="'.$height.'"></td>
								</tr>
								<tr>
									<td height="80" valign="top" style="font-size: 29pt; color: #202020;">'.$produto['info']->label.'<br><span style="font-size: 10pt;">'.$cod_produto.'</span>
									</td>
								</tr>
								<tr>
									<td height="20" style="font-size: 8.5pt">
										<strong>Designer</strong><br>'.$designer.'<br><br>
									</td>
								</tr>
								<tr>
									<td height="20" style="font-size: 8.5pt"><strong>Descri&ccedil;&atilde;o</strong><br>'.$descricao.'<br><br>
									</td>
								</tr>
								<tr>
									<td style="font-size: 8.5pt"><strong>Varia&ccedil;&otilde;es</strong><br><br>
										<table>
											<tr>
												<th style="font-size:8pt;" width="110">COMPRIMENTO</th>
												<th style="font-size:8pt;" width="110">PROFUNDIDADE</th>
												<th style="font-size:8pt;" width="110">ALTURA</th>
												<th style="font-size:8pt; width: 300px" width="270">DESCRIÇÃO</th>
											</tr>
										';

												foreach($produto['variacoes'] as $key => $value):
											
												
											$html .= '<tr><td style="font-size: 8.5pt">'.htmlentities($value->comprimento).' mm</td><td style="font-size: 8.5pt">';
											$html .= htmlentities($value->profundidade).' mm</td><td style="font-size: 8.5pt">';
											$html .= htmlentities($value->altura).' mm</td><td style="font-size: 8.5pt">';
											$html .= htmlentities($value->descricao).'</td></tr>';
						                 endforeach;

									$html .= '</table><br><br>
									</td>
								</tr>
								<tr>
									
								<td style="font-size: 8.5pt"><strong>Acabamentos</strong><br><br>
								';

										if(isset($produto['acabamentos'])):

											//$html .= '<strong> Acabamentos </strong><br>';

											foreach($produto['acabamentos'] as $grupo => $acabamento):

												$html .= '<strong> Acabamentos </strong><br>
															'.$grupo.' - '.$acabamento.'<br>';

											endforeach;

										else:

											//$html .= 'Sem acabamentos dispon&iacute;veis.';

										endif;

							$html .= '</td>
							   	</tr>
							</table>';

					return $html;

				break;
				case "categoria":

					$designer = (!empty($produto['designer']['registro']->nome))?$produto['designer']['registro']->nome:'';
					
					$descricao = (strlen($produto['info']->descricao) > 250) ? substr($produto['info']->descricao, 0, 250). "[..]" : $produto['info']->descricao;
					
					$html = 	'<div style="font-size: 12pt; line-height: 5px; color: #202020; border-bottom: solid 1px #f0f0f0; background: #f0f0f0; margin-bottom: 0; padding: 0px">&nbsp;'.$produto['info']->label.' <span style="font-size: 8pt;"> '.$cod_produto.'</span>									<br>
					<br><table border="0" cellpadding="2">
								<tr>
									<td width="220"><img width="210" src="'.$produto['imagem'].'"></td>
									<td style="font-size: 8.5pt; line-height: 5px; font-family: verdana, arial, sans-serif; padding: 0 10px;">
										<br>
										<strong>Designer</strong>: '.$designer.' <br><br>
										<strong>Descrição</strong><br>'.$descricao.' <br>
									</td>
									<td style="font-size: 9pt; font-family: verdana, arial, sans-serif; padding: 0 10px; ">
										<br><strong>Variações</strong>:<br>';

										foreach($produto['variacoes'] as $key => $value):
											$html .= htmlentities($value->comprimento).'x';
											$html .= htmlentities($value->profundidade).'x';
											$html .= htmlentities($value->altura).'<br>';
						                 endforeach;

						                if(isset($produto['acabamentos'])):
											foreach($produto['acabamentos'] as $grupo => $acabamento):
												$html .= '<strong> Acabamentos </strong><br>
															'.$grupo.' - '.$acabamento.'<br>';
											endforeach;
										else:
												//$html .= '<br>Sem acabamentos dispon&iacute;veis.';
										endif;

							$html .= '</td>
								</tr>
							</table></div>';

					return $html;

				break;
				case "minha-lista":

					$designer = (!empty($produto['designer']['registro']->nome))?$produto['designer']['registro']->nome:'';
					$descricao = (strlen($produto['info']->descricao) > 250) ? substr($produto['info']->descricao, 0, 250)."[..]" : $produto['info']->descricao ;
					
					$html = 	'<table>
								<tr>
									<td width="80" rowspan="2"><img src="'.$produto['imagem'].'"></td>
									<td><strong>'.$produto['info']->label.'</strong></td>
									<td><strong>Designer</strong></td>
									<td><strong>Descrição</strong></td>
									<td><strong>Variações</strong></td>
								</tr>
								<tr>
									<td><span style="font-size: 8.5pt">'.$cod_produto.'</span></td>
									<td>'.$designer.'</td>
									<td>'.$descricao.'</td>
									<td style="font-size: 8pt">';

										foreach($produto['variacoes'] as $key => $value):
											$html .= htmlentities($value->comprimento).'x';
											$html .= htmlentities($value->profundidade).'x';
											$html .= htmlentities($value->altura).'<br>';
						                endforeach;

						                /*if(isset($produto['acabamentos'])):
											foreach($produto['acabamentos'] as $grupo => $acabamento):
												$html .= '<strong> Acabamentos </strong><br>
															'.$grupo.' - '.$acabamento.'<br>';
											endforeach;
										else:
												$html .= '<br>Sem acabamentos dispon&iacute;veis.';
										endif;*/
									$html .= '
									</td>
								</tr>
							</table>';

					return $html;

				break;
			endswitch;







		}




		/**
		 * strDetalhes
		 * @description Prepara a string de exibição dos detalhes da importação
		 * 
		 * @param $inseridos(int) Valor das importações inseridas
		 * @param $atualizados(int) Valor das importações atualizadas
		 * @param $tipo(string) Nome do registro importado
		 *
		 * @return boolean
		 */
		public function strDetalhes($inseridos, $atualizados, $singular, $plural, $genero)
		{
			if($inseridos>1): 
				$str = $inseridos." nov".$genero."s ".$plural." ";
			elseif($inseridos==0):
				$strAg = ($genero=='a') ? 'a' : '';
				$str = "Nenhum".$strAg." ".$singular." nov".$genero." ";
			elseif($inseridos==1):
				$str = "1 nov".$genero." ".$singular." ";
			endif;

			$str .= " e ";

			if($atualizados>1): 
				$str .= $atualizados." ".$plural." atualizad".$genero."s. <br>";
			elseif($atualizados==0):
				$strAg = ($genero=='a') ? 'a' : '';
				$str .= "nenhum".$strAg." ".$singular." atualizad".$genero.". <br>";
			elseif($atualizados==1):
				$str .= "1 ".$singular." atualizad".$genero." <br>";
			endif;

			return $str;
		}
		
		public function makeSlug($nome){
			
			$nome = trim($nome);

			$nome = str_replace("´", "", $nome);
			$nome = str_replace("'", "", $nome);
			$nome = str_replace("`", "", $nome);
			$nome = str_replace(".", "", $nome);
			$nome = str_replace("—", "-", $nome);
			$nome = str_replace("ã", "a", $nome);
			$nome = str_replace("Ã", "a", $nome);
			$nome = str_replace("ç", "c", $nome);
			$nome = str_replace("Ç", "c", $nome);
			$nome = str_replace("Ó", "o", $nome);
			$nome = str_replace("ê", "e", $nome);
			$nome = str_replace("Ê", "e", $nome);
			$nome = str_replace("ó", "o", $nome);
			$nome = str_replace("Ú", "u", $nome);
			$nome = str_replace("ú", "u", $nome);
			$nome = str_replace("á", "a", $nome);
			$nome = str_replace("Á", "a", $nome);
			$nome = str_replace("à", "a", $nome);
			$nome = str_replace("À", "a", $nome);
			$nome = str_replace("é", "e", $nome);
			$nome = str_replace("É", "e", $nome);
			$nome = str_replace("í", "i", $nome);
			$nome = str_replace("Í", "i", $nome);
			$nome = str_replace("ó", "o", $nome);
			$nome = str_replace("Ó", "o", $nome);
			$nome = str_replace("õ", "o", $nome);
			$nome = str_replace("Õ", "o", $nome);
		
			$nome = ereg_replace("[^a-zA-Z0-9_. ]", "", 
		  	strtr($nome, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", 
		  	"aaaaeeiooouucaaaaeeiooouuc"));
			
			$nome     =     str_replace('&', '-e-', $nome);
		    $nome     =     str_replace(' ', '-', $nome);
		    $nome     =     str_replace('--', '-', $nome);
		    $nome     =     str_replace("'", '', $nome);
		    $nome     =     str_replace("`", '', $nome);
		    
		    
		    return strtolower($nome);
		}

		

	    public function enviouPlanilha($html){

	        $dir = PATH_SITE.'wp-content/uploads/debug';
	        $filename = "debug-import-".date('Y-m-d-His', time()).".html";

	        $filepath = $dir."/".$filename;

	        file_put_contents( $dir."/".$filename, $html);

			$txtEmail = 'Nova importação realizada, <a href="'.BASE_URL.'/wp-content/uploads/debug/'.$filename.'">clique aqui</a> para acessar';
			$this->enviarEmail('crosman.bruno@gmail.com, pedro.leite@novoambiente.com.br, camila.wergles@novoambiente.com', "Importação de planilha ".date('Y-m-d-s-i-H', time()), "Importação de planilha ".date('Y-m-d-s-i-H', time()), $txtEmail);
	        
	    }

	    /**
	    * getTiposByFabrica
	    * @description Retorna tipos de acabamentos pelo id da fabrica
	    */
	    public function getTiposByFabrica($idFabrica){

	        $sql = "SELECT t.* 
	                    FROM  na_sys_acabamentos as a, 
	                          na_sys_fabricas as f, 
	                          na_sys_acabamentos_tipos as t 

	                    WHERE   f.id_fabrica = a.id_fabrica AND 
	                            a.id_tipo = t.id_tipo AND 
	                            f.id_fabrica = ".$idFabrica." 

	                    GROUP BY a.id_tipo";

	        $query = mysql_query($sql);

	            return $query;

	    }
	    
	    /**
		 * getTitleExcerpt
		 * @description Retorna o Excerpt de uma string
		 * 
		 * @param string $word Palavra a ser reduzida
		 * @param int $length Tamanho desejado
		 * @param string $replace Caracteres a serem colocados no fim da string
		 * 
		 * @return string $string Texto reduzido (caso necessário)
		 */
		public function getTitleExcerpt($word, $length, $replace=' [..]')
		{
			$stripped = strip_tags($word);
			if(strlen($word) > $length):
			
				$string = substr($stripped, 0, $length);
				return $string.$replace;
			else:
				
				return $stripped;
			endif;
		}

        public function convertFromCP1252($string)
        {
            $search = array('&',
                            '<',
                            '>',
                            '"',
                            chr(212),
                            chr(213),
                            chr(210),
                            chr(211),
                            chr(209),
                            chr(208),
                            chr(201),
                            chr(145),
                            chr(146),
                            chr(147),
                            chr(148),
                            chr(151),
                            chr(150),
                            chr(133),
                            chr(194)
                        );
        
             $replace = array(  '&amp;',
                                '&lt;',
                                '&gt;',
                                '&quot;',
                                '&#8216;',
                                '&#8217;',
                                '&#8220;',
                                '&#8221;',
                                '&#8211;',
                                '&#8212;',
                                '&#8230;',
                                '&#8216;',
                                '&#8217;',
                                '&#8220;',
                                '&#8221;',
                                '&#8211;',
                                '&#8212;',
                                '&#8230;',
                                ''
                            );
        
                    return str_replace($search, $replace, $string.' passei no metodo');
        }

		
	}