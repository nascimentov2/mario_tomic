<?php 
if( !isset($_SESSION) ){ session_start(); }

class registro extends Registros {
    
    /**
     * getVariacaoByID
     * @description Recupera a oferta pelo ID
     * 
     * @param $id_oferta(int) ID da oferta
     * @param $security(boolean) Se passado como true, o método só retornará o resultado se o owner for igual o usuário logado
     * 
     * @return $detalhes_oferta(array) Detalhes da oferta
     */
    public function getVariacaoByID($id_variacao)
    {
    	$this->tabela = DB_VARIACOES;
        $objVariacao = $this->get('', 'WHERE id_variacao = '.$id_variacao, 'objeto');

        if (count($objVariacao)<0):

				error_log('Erro na consulta ao banco de dados. Em Produtos -> Detalhe -> Variações do produto. Mysql error:' . mysql_error());
			    return $result = array('value' => 0, 'registro' => 'Erro de acesso. Por favor, entre em contato com o administrador.');
				
		elseif (count($objVariacao) == 0):
		    
		    return $result = array('value' => 0, 'registro' => 'Variação do produto não encontrada.');
		
		elseif (count($objVariacao) > 1):

			return $result = array('value' => 0, 'registro' => 'Produto corrompido.  Por favor, entre em contato com o administrador.');
		
		else:

			return $result = array('value' => 1, 'registro' => $objVariacao);

		endif;

		return $result;

    }

    /**
     * setInfosProduto
     * @description Edita informações de um produto
     * 
     * @param $data(array) Post com dados da oferta
     * 
     * @return boolean
     */
    public function setInfosProduto($data)
    {
        if($this->validaCampos($data)):
            
            $id_produto = $data['id_produto'];
        	unset($data['id_produto']);
        	unset($data['editar']);

            $this->tabela = DB_PRODUTOS;
            $this->index = 'id_produto';

            $this->set($id_produto, $data);
            $_SESSION['retorno']['classe'] = 'sucesso';
            $_SESSION['retorno']['mensagem'] = 'Produto editado com sucesso!';
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = $this->invalidFields;
        endif;
        
    }


    /**
     * excluir
     * @description Remove um produto
     * 
     * @param $itemid(array) Dados do produto
     * 
     * @return boolean
     */
    public function delProduto($itemid)
    {

    	$this->tabela = DB_PRODUTOS;
        $this->index = 'id_produto';

        $query = $this->getSql("DELETE FROM ".DB_VARIACOES." WHERE id_produto = ".$itemid);
        if ($query):
        	$queryDelProduto = $this->delete($itemid);    
            if ($queryDelProduto):
                $_SESSION['retorno']['classe'] = 'sucesso';
                $_SESSION['retorno']['mensagem'] = 'Produto excluido com sucesso.';
    		else:
                $_SESSION['retorno']['classe'] = 'erro';
                $_SESSION['retorno']['mensagem'] = 'Ocorreu um erro ao excluir o produto.';
    		endif;
		else:
		    $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Ocorreu um erro ao excluir o produto.';
		endif;
    			
    }

    /**
     * delProdutos
     * @description Remove produtos pelos id's passados
     * 
     * @param $arrayIds(array) Ids dos produtos a serem excluídos
     * 
     */
    public function delProdutos($arrayIds)
    {
        print "<pre>"; print_r($arrayIds); die;
        /*
        $this->tabela = DB_PRODUTOS;
        $this->index = 'id_produto';

        $query = $this->getSql("DELETE FROM ".DB_VARIACOES." WHERE id_produto = ".$itemid);
        if ($query):
            $queryDelProduto = $this->delete($itemid);    
            if ($queryDelProduto):
                $_SESSION['retorno']['classe'] = 'sucesso';
                $_SESSION['retorno']['mensagem'] = 'Produto excluido com sucesso.';
            else:
                $_SESSION['retorno']['classe'] = 'erro';
                $_SESSION['retorno']['mensagem'] = 'Ocorreu um erro ao excluir o produto.';
            endif;
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Ocorreu um erro ao excluir o produto.';
        endif;*/
        $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_acabamentos';
                
    }

    /**
     * delAcabamento
     * @description Remove um acabamento
     * 
     * @param $data(array) Array do post com info's do acabamento
     * 
     * @return boolean
     */
    public function delAcabamento($data)
    {

        $this->tabela = DB_ACABAMENTOS;
        $this->index = 'id_acabamento';

        $queryDel = $this->delete($data['itemid']);    
        if ($queryDel):
            $_SESSION['retorno']['classe'] = 'sucesso';
            $_SESSION['retorno']['mensagem'] = 'Acabamento excluido com sucesso.';
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Ocorreu um erro ao excluir o acabamento.';
        endif;

        $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_acabamentos';
                
    }

    /**
     * delAcabamento
     * @description Remove um acabamento
     * 
     * @param $data(array) Array do post com info's do acabamento
     * 
     * @return boolean
     */
    public function delVariacaoProduto($id_variacao, $id_post)
    {

        $this->tabela = DB_VARIACOES;
        $this->index = 'id_variacao';

        $queryDelVariacoes = $this->delete($id_variacao);

        $this->tabela = DB_VALORES;
        $this->index = 'id_variacao';

        $queryDelValores = $this->delete($id_variacao);

        $redirect = BASE_URL.'wp-admin/post.php?post='.$id_post.'&action=edit';
        print '<p> Variação excluída com sucesso. Aguarde enquanto a página é redirecionada ou <a href="'.$redirect.'"> Clique aqui </a> para retornar.';

        sleep(10);
        print '<script> location.href="'.$redirect.'"; </script>';
    }

    /**
     * createPostDesign
     * @description Insere um post no banco de dados do wordpress 
     *              e adiciona id_post na tabela de design
     * 
     * @param $arrDesign(array) Array do post enviado
     *
     */

    public function createPostDesign($arrDesign)
    {
        $current_user = wp_get_current_user();

        // Criando objeto post para WP
        $my_post = array(
          'post_title'    => $arrDesign['nome'],
          'post_type'     => 'designer',
          'post_content'  => '',
          'post_status'   => 'publish',
          'post_author'   => $current_user->ID,
          'post_category' => array(2,2)
        );
        // Insere post no DB e retorna ID do post
        $post_id = wp_insert_post( $my_post );

        if($post_id):
            add_post_meta($post_id, 'id_parent', $arrDesign['id_design'], true);

            $this->tabela = DB_DESIGNERS;
            $this->index = 'id_designer';

            $id_designer = $arrDesign['id_design'];
            $data = array('id_post' => $post_id);

            $result = $this->set($id_designer, $data);

            if($result):
                $_SESSION['retorno']['classe'] = 'sucesso';
                $_SESSION['retorno']['mensagem'] = 'Designer publicado com sucesso.';
            else:
                $_SESSION['retorno']['classe'] = 'erro';
                $_SESSION['retorno']['mensagem'] = 'Designer não publicado. Por favor, entre em contato com o administrador.';
            endif;

            $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_produtos';
        else:

            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Designer não publicado. Por favor, entre em contato com o administrador.';

        endif;
        
    }

    /**
     * createPostProduto
     * @description Insere um post no banco de dados do wordpress 
     *              e adiciona id_post na tabela de design
     * 
     * @param $arrProduto(array) Array do post enviado
     *
     */
    public function createPostProduto($arrProduto)
    {   
        $current_user = wp_get_current_user();

        // Criando objeto post para WP
        $my_post = array(
          'post_title'    => $arrProduto['nome'],
          'post_type'     => 'produto',
          'post_content'  => $arrProduto['descricao'],
          'post_status'   => 'publish',
          'post_author'   => $current_user->ID,
          'post_category' => array(1,1)
        );

        // Insere post no DB e retorna ID do post
        $post_id = wp_insert_post( $my_post );

        $taxonomies = get_terms('fabrica');

        wp_set_object_terms( $post_id, $arrProduto['fabrica'], 'fabrica' );

        if($post_id):
			
            if($arrProduto['img_result']):

                // Adicionando imagem ao post
                foreach($arrProduto['img'] as $keyImage => $valImage):
                    $this->addImgQueue($post_id, $valImage);
                endforeach;

            endif;

            add_post_meta($post_id, 'id_parent', $arrProduto['id_produto'], true);
            add_post_meta($post_id, 'ordem_na_prateleira', '5.0');

            $this->tabela = DB_PRODUTOS;
            $this->index = 'id_produto';

            $id_produto = $arrProduto['id_produto'];
            $data = array('id_post' => $post_id, 'status' => 'publicado');

            $result = $this->set($id_produto, $data);

            if($result):
                $_SESSION['retorno']['classe'] = 'sucesso';
                $_SESSION['retorno']['mensagem'] = 'Produto publicado com sucesso.';
            else:
                $_SESSION['retorno']['classe'] = 'erro';
                $_SESSION['retorno']['mensagem'] = 'Produto não publicado. Por favor, entre em contato com o administrador.';
            endif;

            $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_core';
        else:

            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Produto não publicado, por favor, entre em contato com o administrador.';

        endif;
        
    }

    /**
    * removeImgQueue
    * @description Retira imagem da fila de upload
    * 
    */
    public function getImgsQueue($table='na_sys_img_queue', 
                   $where=" id_img_queue <> 0 ",
                   $limit=array('inicio' => 0, 'limite' => '100000'), 
                   $order=array('campo' => 'id_img_queue', 'tipo' => 'DESC')){

        //Retorna os registros de acordo com o filtro
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$table.' 
            WHERE '.$where.'
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
    * addImgQueue
    * @description Adiciona imagem a fila para inserção no post
    * 
    */
    public function addImgQueue($id_post, $url_img){

        $sql = "INSERT INTO ".DB_IMG_QUEUE." 
                    ( id_post, url_imagem, status ) VALUES
                    ( ".$id_post.", '".$url_img."', 'pendente')";
        
        $query = mysql_query($sql);

    }

    /**
    * removeImgQueue
    * @description Retira imagem da fila de upload
    * 
    */
    public function removeImgQueue($id_img_queue, $attach_id){

        $this->tabela = DB_IMG_QUEUE;
        $this->index = 'id_img_queue';

        $identificador = $id_img_queue;
        $data = array('status' => 'enviado', 'url_imagem_wp' => $attach_id);

        $result = $this->set($identificador, $data);

    }

    /**
    * setImgQueue
    * @description Edita link img_queue
    * 
    */
    public function setImgQueue($data){

        $this->tabela = DB_IMG_QUEUE;
        $this->index = 'id_img_queue';

        $identificador = $data['id_img_queue'];

        unset($data['id_img_queue']);
        unset($data['id_produto']);

        $result = $this->set($identificador, $data);

        if($result):
            $_SESSION['retorno']['classe'] = 'sucesso';
            $_SESSION['retorno']['mensagem'] = 'Link alterado com sucesso.';
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Link não alterado. Por favor, entre em contato com o administrador.';
        endif;

        $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_processa_imagens&lista=falha';
    }

    /**
    * uploadImgQueue
    * @description Pega na tabela img_queue a primeira imagem da fila e insere no post
    * 
    */
    public function uploadImgQueue(){

        set_time_limit(0);

        $queryImagem = $this->getSql("SELECT * FROM ".DB_IMG_QUEUE." WHERE status = 'pendente' ORDER BY id_img_queue ASC LIMIT 1");
        
        $objImagemQueue = mysql_fetch_object($queryImagem);
        
        // Define onde será salvo
        $filename = $objImagemQueue->id_post.'_'.date('Ymd-Hms').'_'.strtolower(basename($objImagemQueue->url_imagem));
        
        // Busca a imagem na URL
        if (strlen($objImagemQueue->url_imagem) > 4):
          
            $uploads = wp_upload_dir();
            
            $info = @getimagesize($objImagemQueue->url_imagem);
            
            if (isset($info["mime"])):
  
                $content = @file_get_contents($objImagemQueue->url_imagem);

                if($content==true):

                    $path_file_full = $uploads['path'] .'/'. $filename;
        
                    file_put_contents($path_file_full, $content);
                    
                      $wp_filetype = wp_check_filetype(basename($filename), null );
                      $attachment = array(
                          'guid' => $uploads['url'].'/'.$filename, 
                          'post_mime_type' => $wp_filetype['type'],
                          'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                          'post_content' => '',
                          'post_status' => 'inherit'
                      );
                      $attach_id = wp_insert_attachment( $attachment, $path_file_full, $objImagemQueue->id_post );
                      //print "Inseriu wp_insert_attachment, attach_id: ".$attach_id." no post id: ".$objImagemQueue->id_post."<br>";
                      //error_log("imagem inserida no WP wp_insert_attachment");
                    if ($attach_id != 0):

                      //print "Entrou no if attach_id != 0<br>";
                      //error_log("antes de wp_generate_attachment_metadata");
                      $attach_data = wp_generate_attachment_metadata( $attach_id, $path_file_full);
                      wp_update_attachment_metadata( $attach_id, $attach_data );
                      //if(empty($teste)):
                        ///error_log("falhou");
                      //else:
                        //error_log("wp_generate_attachment_metadata NÃO É VAZIO");
                      //endif;
                      //$attach_update = wp_update_attachment_metadata( $attach_id, $attach_data );
                      //error_log("depois");
                      //print "attach_data: ".$attach_data."<br>";
                      //print "attach_update: ".$attach_update."<br>";

                      $sqlExisteThumb = $this->getSql("SELECT * FROM ".DB_IMG_QUEUE." WHERE status = 'enviado' AND id_post = ".$objImagemQueue->id_post." LIMIT 1");
                      if($sqlExisteThumb&&!mysql_num_rows($sqlExisteThumb)):
                          //print "Inseriu attach_id: ".$attach_id." no post id: ".$objImagemQueue->id_post."<br>";
                          update_post_meta($objImagemQueue->id_post, '_thumbnail_id', $attach_id);
                      else:
                        //print "NÃO!!! Inseriu attach_id: ".$attach_id." no post id: ".$objImagemQueue->id_post.", pois já encontrou img_queue com o mesmo id_post enviado e que possui imagem<br>";
                      endif;
 
                      $this->removeImgQueue($objImagemQueue->id_img_queue, $attach_id);
                      //print "Imagem: ".$filename." anexada ao post-id ".$objImagemQueue->id_post;
                      //die;
                      /*
                      $txtEmail = "Imagem foi enviada com SUCESSO: ".$objImagemQueue->url_imagem."<hr>";
                      $txtEmail .= print_r($info, true);
                      $this->enviarEmail('thalles@homemmaquina.com.br', "Envio de imagem ".date('Y-m-d-s-i-H', time()), "Envio de imagem ".date('Y-m-d-s-i-H', time()), $txtEmail);
                      */

                    endif;

                endif;

            else:

                  
                  $txtEmail = "Imagem FALHOU. URL:  ".$objImagemQueue->url_imagem.". <hr> ";
                  $txtEmail .= print_r($info, true);
                  $this->enviarEmail('crosman.bruno@gmail.com, pedro.leite@novoambiente.com.br, camila.wergles@novoambiente.com', "Envio de imagem ".date('Y-m-d-s-i-H', time()), "Envio de imagem ".date('Y-m-d-s-i-H', time()), $txtEmail);
                  

                  $this->tabela = DB_IMG_QUEUE;
                  $this->index = 'id_img_queue';
                  $identificador = $objImagemQueue->id_img_queue;

                  $data['status'] = 'falha';

                  $this->set($identificador, $data);
                  print "Nenhuma imagem anexada: FALHA";

            endif;
      
        endif;
        
        die;

    }

    /**
    * editarFlag
    * @description Edita informações de flags
    * 
    * @param $data(array) Array enviado
    */
    public function editarFlag($data){

        $flag = $data['nmf'];
        $id_produto = $data['idp'];
        
        $this->tabela = DB_PRODUTOS;
        $obj = $this->get($flag, 'WHERE id_produto = '.$id_produto, 'objeto');
        
        $newValue = ($obj->$flag)?0:1;
        
        $identificador = $id_produto;
        $this->index = 'id_produto';
        $atualizaResponsavel = $this->set($identificador, array(
            $flag => $newValue
        ));

    }

    /**
    * excluirProdutos
    * @description Exclui produtos em massa
    * 
    * @param $data(array) Array com o formulário enviado
    */
    public function excluirProdutos($data){

        print_r($data);/*
        $identificador = $data['ido'];
        $atualizaResponsavel = $this->set($identificador, array(
            'id_usuario' => $data['idr']
        ));*/

    }


    /**
    * setVariacaoProd
    * @description Edita uma variação de produto
    * 
    */
    public function setVariacaoProd($data){

        $id_produto = $data['id_produto'];
        $id_variacao = $data['id_variacao'];
        $id_post = $data['id_post'];
        unset($data['id_produto']);
        unset($data['id_variacao']);
        unset($data['id_post']);

        $this->tabela = DB_VARIACOES;
        $this->index = 'id_variacao';

        $identificador = $id_variacao;

        $result = $this->set($identificador, $data);

        if($result):
            $_SESSION['retorno']['classe'] = 'sucesso';
            $_SESSION['retorno']['mensagem'] = 'Variação alterada com sucesso, <a href="'.BASE_URL.'wp-admin/post.php?post='.$id_post.'&action=edit"> clique aqui </a> para voltar a edição do produto publicado.';
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Erro ao editar variação. Por favor, entre em contato com o administrador.';
        endif;

        $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value='.$id_variacao.'&idProd='.$id_produto;
    }

	/**
	 * addVariacaoProduto
	 * 
	 * @description Adiciona uma nova variação ao produto
	 */
	 public function addVariacaoProduto($data)
	 {
	 	$id_produto = $data['id_produto'];
		$id_post = $data['id_post'];
        unset($data['id_variacao']);
		unset($data['id_post']);
		
		$this->tabela = DB_VARIACOES;
        $this->index = 'id_variacao';
		
		$data['gid_descricao'] = $this->makeSlug(utf8_encode($data['descricao']));
		
		$result = $this->insert($data, true);
		
		if($result):
            $_SESSION['retorno']['classe'] = 'sucesso';
            $_SESSION['retorno']['mensagem'] = 'Variação incluida com sucesso, <a href="'.BASE_URL.'wp-admin/post.php?post='.$id_post.'&action=edit"> clique aqui </a> para voltar a edição do produto publicado.';
        else:
            $_SESSION['retorno']['classe'] = 'erro';
            $_SESSION['retorno']['mensagem'] = 'Erro ao editar variação. Por favor, entre em contato com o administrador.';
        endif;

        $_SESSION["globalRedirectUrl"] = 'wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value='.$result.'&idProd='.$id_produto;
				
				
	 }

}