<?php
/**
Plugin Name: Novo Ambiente
Plugin URI: http://homemmaquina.com.br
Description: Administrador de produtos Novo Ambiente
Version: 1.0
Author: Homem Máquina
Author URI: http://homemmaquina.com.br
*/

include('system/core.php');

define("PLUGIN_DIR","wp-content/plugins/novoambiente-core.php");

// Atualizar dados do produto quando um post for editado
//add_action( 'save_post ', 'save_post_to_sys' );

add_action( 'pre_post_update', 'edit_postid' );

function make_slug($nome)
{
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

function save_post_to_sys($info_produto){
	
	if (!defined('CONN'))
        define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
        mysql_select_db( DB_NAME, mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
	
	$query = "INSERT INTO ".DB_PRODUTOS." (id_fabrica, id_designer, id_post, cod_produto, label, gid_produto, descricao) 
								VALUES (".$info_produto['id_fabrica'].", ".$info_produto['id_designer'].", ".$info_produto['id_post'].", '".$info_produto['cod_produto']."', '".$info_produto['label']."', '".$info_produto['gid_produto']."', '".$info_produto['descricao']."')";
	$result = mysql_query($query);

	
	//@TODO criar log de erro
}

function edit_postid( $postid ){

    global $post_type;
    if ( $post_type != 'produto' ) return;
	
	//Verifica se o post já existe na tabela de produtos, se não exitir cria o produto associado ao post e deixa o método prosseguir
	if (!defined('CONN'))
        define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
        mysql_select_db( DB_NAME, mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
	
	$verifica_post = "SELECT id_post FROM ".DB_PRODUTOS." WHERE id_post = ".$postid;
	$query = mysql_query($verifica_post);
	

    set_thumbnail_cache($postid, get_the_post_thumbnail($postid, 'thumb-home'));
	
    if(mysql_num_rows($query) < 1):
		
		/** $id_fabrica = array_pop($_POST['tax_input']['fabrica']);
		$id_designer = $_POST['designer'];
		$id_post = $_POST['post_ID'];
		$cod_produto = $_POST['cod_produto'];
		$label = $_POST['post_title'];
		$gid_produto = make_slug($label);
		$descricao = $_POST['content'];
		
		
		$info_produto = array(	'id_fabrica' => $id_fabrica, 
								'id_designer' => $id_designer, 
								'id_post' => $id_post, 
								'cod_produto' => $cod_produto, 
								'label' => $label, 
								'gid_produto' => $gid_produto, 
								'descricao' => $descricao);
		
		//@TODO Falta validar os campos obrigatórios 
		
		save_post_to_sys($info_produto); */
	
	echo utf8_encode('<h4 style="color: red">N&atilde;o &eacute; poss&iacute;vel adicionar um novo produto diretamente pelo Wordpress.</h4>');
	die();
		
	else:
		//echo 'existe';
	endif;
   
   //print "<pre>"; print_r($_POST); print "</pre>"; die;

    // se for edição rápida
  	if(isset($_POST['action'])&&$_POST['action']=='inline-save'):
        //Doing the connection
       /*if (!defined('CONN'))
            define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
  
        mysql_select_db( DB_NAME, mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
       */
        $ordem_na_prateleira = get_post_meta( $_POST['post_ID'], "ordem_na_prateleira", true );

        if ($ordem_na_prateleira): 
            // Não faz nada
        else:
            // Define a ordem padrão = 5.0
            add_post_meta($_POST['post_ID'], "ordem_na_prateleira", "5.0");
        endif;

    endif;
    
    // se for edição normal
    if(isset($_POST['action'])&&$_POST['action']=='editpost'):
      //Doing the connection
        if (!defined('CONN'))
            define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
  
        mysql_select_db( DB_NAME, mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );

        $cod_produto = isset($_POST['cod_produto'])?$_POST['cod_produto']:'';
        $novidade = isset($_POST['novidade'])?$_POST['novidade']:0;
        $medida_especial = isset($_POST['medida_especial'])?$_POST['medida_especial']:0;
        $low_cost = isset($_POST['low_cost'])?$_POST['low_cost']:0;
        $in_ecommerce = isset($_POST['in_ecommerce'])?$_POST['in_ecommerce']:0;
        $ecommerce_link = isset($_POST['ecommerce_link'])?$_POST['ecommerce_link']:'';

        $update_designer = (!empty($_POST['designer'])) ? "id_designer = ".$_POST['designer'].", " : '';

        $sql = "UPDATE ".DB_PRODUTOS." SET  cod_produto = '".$cod_produto."',
                                            novidade = ".$novidade.",
                                            medida_especial = ".$medida_especial.",
                                            low_cost = ".$low_cost.",
                                            in_ecommerce = ".$in_ecommerce.", $update_designer
                                            ecommerce_link = '".$ecommerce_link."'
                                      WHERE id_post = ".$postid;
        $consulta = mysql_query($sql);
        if(mysql_affected_rows()>0):
            //print "Dados alteradas com sucesso na tabela de produtos.";
        else:
            //print "Erro ao alterar dados na tabela de produtos.";
        endif;

    endif;

}
//error_log("pre_post_update");
    
//save post só é chamado quando algum campo do post é realmente alterado. Usar pre_post_update em vez disso.  

//$var = add_action( 'save_post ', 'edit_postid' );


add_action('admin_menu', 'menu_novo_ambiente');

//Adicionar itens de menu

function menu_novo_ambiente() {

	add_menu_page( 'Base de dados', 'Base de dados', 'upload_files', 'novo_ambiente_core', 'novo_ambiente_get_core', get_bloginfo('url').'/favicon.png', '4.1');
	add_submenu_page( 'novo_ambiente_core', 'Produtos', 'Produtos', 'upload_files', 'novo_ambiente_core', 'novo_ambiente_get_core');
	add_submenu_page( 'novo_ambiente_core', 'Designers', 'Designers', 'upload_files', 'novo_ambiente_produtos', 'novo_ambiente_designers');
	add_submenu_page( 'novo_ambiente_core', 'Fábricas', 'Fábricas', 'upload_files', 'novo_ambiente_fabricas', 'novo_ambiente_fabricas');
	add_submenu_page( 'novo_ambiente_core', 'Acabamentos', 'Acabamentos', 'upload_files', 'novo_ambiente_acabamentos', 'novo_ambiente_acabamentos');
	add_submenu_page( 'novo_ambiente_core', 'Usuários', 'Usuários', 'upload_files', 'novo_ambiente_usuarios', 'novo_ambiente_usuarios');
  add_submenu_page( 'novo_ambiente_core', 'Importar .xls', 'Importar .xls', 'upload_files', 'novo_ambiente_importar', 'novo_ambiente_importar');
  add_submenu_page( 'novo_ambiente_core', 'Processamento de Imagens', 'Processamento de Imagens', 'upload_files', 'novo_ambiente_processa_imagens', 'novo_ambiente_processa_imagens');
}

function novo_ambiente_get_core(){
	include("app/view/produtos.php");
}


function novo_ambiente_designers(){
	include("app/view/designers.php");
}

function novo_ambiente_fabricas(){
	include("app/view/fabricas.php");
}
function novo_ambiente_acabamentos(){
	include("app/view/acabamentos.php");
}
function novo_ambiente_importar(){
	include('app/view/includes/action-incluir-produto.php');
}
function novo_ambiente_processa_imagens(){
  include('app/view/includes/processamento-imagens.php');
}
function novo_ambiente_usuarios(){
  include('app/view/usuarios.php');
}


//Adicionar scripts no nosso plugin

function novo_ambiente_print(){

	wp_register_script( 'tipsy_script', plugins_url('_static/js/jquery.tipsy.js', __FILE__) , 'jquery');
	wp_register_script( 'novo_ambiente_script', plugins_url('_static/js/custom.js', __FILE__) );
	wp_register_style( 'novo_ambiente_style', plugins_url('_static/css/custom.css', __FILE__) );
 	wp_enqueue_script( 'tipsy_script' );
 	wp_enqueue_script( 'novo_ambiente_script' );
 	wp_enqueue_style( 'novo_ambiente_style' );
}
add_action('admin_print_styles', 'novo_ambiente_print');

function novo_ambiente_add_post_to_sys(){
	
}

// Adicionar custom post type - SISTEMA
function cpt_sistema() {

  $labels = array(
    'name' => 'Sistema',
    'singular_name' => 'Sistema',
    'add_new' => 'Adicionar',
    'add_new_item' => 'Adicionar Sistema',
    'edit_item' => 'Editar Sistema',
    'new_item' => 'Novo Sistema',
    'all_items' => 'Todos os Sistemas',
    'view_item' => 'Ver Sistema',
    'search_items' => 'Procurar Sistema',
    'not_found' =>  'Nenhum Sistema encontrado',
    'not_found_in_trash' => 'Nenhum Sistema na lixeira', 
    'parent_item_colon' => '',
    'menu_name' => 'Sistema'
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'menu_icon' => get_bloginfo('url').'/favicon.png', 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'sys' ),
    'capability_type' => 'page',
    'has_archive' => false, 
    'hierarchical' => true,
    'menu_position' => 7,
    'supports' => array( 'title', 'thumbnail')
  ); 

  register_post_type( 'sistema', $args );
}
add_action( 'init', 'cpt_sistema' );


// Adicionar custom post type - DESIGNER
function cpt_designers() {

  $labels = array(
    'name' => 'Designers',
    'singular_name' => 'Designer',
    'add_new' => 'Adicionar',
    'add_new_item' => 'Adicionar Designer',
    'edit_item' => 'Editar Designer',
    'new_item' => 'Novo Designer',
    'all_items' => 'Todos os Designers',
    'view_item' => 'Ver Designer',
    'search_items' => 'Procurar Designer',
    'not_found' =>  'Nenhum designer encontrado',
    'not_found_in_trash' => 'Nenhum designer na lixeira', 
    'parent_item_colon' => '',
    'menu_name' => 'Designers'
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'menu_icon' => get_bloginfo('url').'/favicon.png', 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'designer' ),
    'capability_type' => 'page',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => 7,
    'supports' => array( 'title', 'editor', 'thumbnail')
  ); 

  register_post_type( 'designer', $args );
}
add_action( 'init', 'cpt_designers' );

// Adicionar custom post type - PRODUTOS
function cpt_produtos() {

  $labels = array(
    'name' => 'Produtos',
    'singular_name' => 'Produto',
    'add_new' => 'Adicionar produto',
    'add_new_item' => 'Adicionar novo Produto',
    'edit_item' => 'Editar produto',
    'new_item' => 'Adicionar produto',
    'all_items' => 'Todos os produtos',
    'view_item' => 'Ver produto',
    'search_items' => 'Procurar produtos',
    'not_found' =>  'Produto não encontrado',
    'not_found_in_trash' => 'Produto não encontrado na lixeira', 
    'parent_item_colon' => '',
    'menu_name' => 'Produtos'
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'menu_icon' => get_bloginfo('url').'/favicon.png', 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'produto' ),
    'capability_type' => 'page',
    'has_archive' => false, 
    'hierarchical' => false,
    'menu_position' => 4,
    'supports' => array( 'title', 'editor', 'thumbnail')
  ); 

  register_post_type( 'produto', $args );
}
add_action( 'init', 'cpt_produtos' );


// Retirar post id da tabela de Designers quando for excluido permanentemente no WP
function del_postid( $postid ){

    global $post_type;   
    if ( $post_type != 'designer' && $post_type != 'produto' ) return;

    /** Doing the connection */
    if (!defined('CONN'))
        define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
    
    mysql_select_db( DB_NAME, CONN );

    if($post_type=='designer')
        $tabela = DB_DESIGNERS;
    if($post_type=='produto')
        $tabela = DB_PRODUTOS;

    $sql = "UPDATE ".$tabela." SET id_post = 0 WHERE id_post = ".$postid;
    $consulta = mysql_query($sql);
    if(mysql_affected_rows()>0):
        //print "Conteúdo despublicado na tabela rascunho.";
    else:
        //print "Erro ao despublicar na tabela rascunho.";
    endif;
}
add_action( 'before_delete_post', 'del_postid' );

//var_dump($var);
//print_r($var);

/* Define the custom box */

add_action( 'add_meta_boxes', 'add_custom_box' );

/* Adds a box to the main column on the Post and Page edit screens */
function add_custom_box() {
        add_meta_box( 'box-produto', __( 'Variações do produto', 'novoambiente_textdomain' ), 'htmlBox', 'produto' );
}

/* Prints the box content */
function htmlBox( $post ) {

    $registros = new registro;
    $label = 'id_post';
    $value = $post->ID;
    //print_r($post); die;

    $paginate = array('inicio' => 0, 'limite' => '100000');
    $consulta = $registros->getRegistro(DB_PRODUTOS, $label, $value);
	
	//@update só adiciona a possibilidade de inserir variação se o produto existe na tabela SYS
	if($consulta['value'] == 1):
	
    $c_variacao = $registros->getRegistros(DB_VARIACOES.' WHERE id_produto = '.$consulta['registro']->id_produto, $paginate, array('campo' => 'id_produto', 'tipo' => 'DESC'));

    
    echo '<table class="wp-list-table widefat fixed variacoes">    
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Comprimento</th>
                                <th>Profundidade</th>
                                <th>Altura</th>
                                <th>Descrição</th>
                                <th colspan="3">Opções</th>
                            </tr>
                        </thead>
                        <tfoot>
                        	<tr>
                        		<th colspan="7"><a class="row-title" href="'.get_bloginfo('url').'/wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&action=adicionar&idProd='.$consulta['registro']->id_produto.'">Incluir</a></td></th>
                        	</tr>
                        </tfoot>
                        <tbody>';
                        
                            while($ov = mysql_fetch_object($c_variacao['result'])):
                                
                                $txtDel = '"Deseja realmente excluir esta variação ?"';
                                echo "<tr>
                                        <td>".$ov->comprimento."</td>
                                        <td>".$ov->profundidade."</td>
                                        <td>".$ov->altura."</td>
                                        <td>".$ov->descricao."</td>
                                        <td><a class='row-title' href='".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value=".$ov->id_variacao."&idProd=".$consulta['registro']->id_produto."'>Editar</a></td>
                                        <td><a class='row-title' href='".get_bloginfo('url')."/wp-admin/admin.php?page=novo_ambiente_core&view=detalheVariacao&value=".$ov->id_variacao."&idProd=".$consulta['registro']->id_produto."&del=1&idPost=".$post->ID."' onclick='return confirm(".$txtDel.")'>Excluir</a></td>
                                     </tr>
                                    </tr>";     
                                
                            endwhile;
                        
                 echo '</tbody></table>
                </td>
            </tr>
        </table>';
	else:
	
		echo '<p>Não é possível adicionar variações antes de importar um produto.</p>';
	
	endif;

}

add_action( 'add_meta_boxes', 'add_boxDados_produto' );

/* Adds a box to the main column on the Post and Page edit screens */
function add_boxDados_produto() {
        add_meta_box( 'box-dados-produto', __( 'Dados do produto', 'novoambiente_textdomain' ), 'htmlBoxDados', 'produto' );
}

/* Prints the box content */
function htmlBoxDados( $post ) {

    $registros = new registro;
    $label = 'id_post';
    $value = $post->ID;
    //print_r($post); die;

    $paginate = array('inicio' => 0, 'limite' => '100000');
    $consulta = $registros->getRegistro(DB_PRODUTOS, $label, $value);
    
    $designers = $registros->getActiveDesigners();

    //print_r($designers);
    //print_r($consulta['registro']);
    $ckdNovidade = ($consulta['registro']->novidade==1)?'checked="checked"':'';
    $ckdMedida_especial = ($consulta['registro']->medida_especial==1)?'checked="checked"':'';
    $ckdLow_cost = ($consulta['registro']->low_cost==1)?'checked="checked"':'';
    $ckdEcommerce = ($consulta['registro']->in_ecommerce==1)?'checked="checked"':'';
    $id_designer = ($consulta['registro']->id_designer);;
    echo '<div id="titlewrap">
            <div style="border-bottom:1px solid #dfdfdf; margin-bottom: 1em; padding-bottom: 1em;">
              <label for="cod_produto" style="display:block; margin:0 0 0.5em;"><strong>Código</strong></label>
              <input type="text" name="cod_produto" size="30" value="'.$consulta['registro']->cod_produto.'" id="title" autocomplete="off"><br>
            </div>
            <div class="designer_list"  style="border-bottom:1px solid #dfdfdf; margin-bottom: 1em; padding-bottom: 1em;">
              <label for="desigenr" style="display:block; margin:0 0 0.5em;"><strong>Designer</strong></label>
            <select name="designer"> 
                <option value="0">- Selecione -</option>
            ';
    
    while ($objDesigner = mysql_fetch_object($designers['result'])):
        
      $checked = ($id_designer == $objDesigner->id_designer) ? 'selected="selected"' : "";
      $draft = ($objDesigner->id_post == 0) ? ' (não publicado)' : "";
 
      echo "<option value='".$objDesigner->id_designer."' $checked>".$objDesigner->nome."$draft</option>";
    endwhile;
    
    echo  ' </select></div>
            <div style="border-bottom:1px solid #dfdfdf; margin-bottom: 1em; padding-bottom: 1em;">
                <strong style="display:block; margin:0 0 0.5em;">Flags</strong>
                <input '.$ckdNovidade.' type="checkbox" value="1" name="novidade" style="margin-bottom:0.5em;"> Novidade <br>
                <input '.$ckdMedida_especial.' type="checkbox" value="1" name="medida_especial"> Medida Especial <br>
                <input '.$ckdLow_cost.' type="checkbox" value="1" name="low_cost"> Low Cost <br>
            </div>
            <div>
                <strong style="display:block; margin:0 0 0.5em;">Loja virtual</strong>
                <input '.$ckdEcommerce.' type="checkbox" value="1" name="in_ecommerce" style="margin-bottom:0.5em;"> Item cadastrado na loja virtual

                <strong style="display:block; margin:1em 0 0.5em;">Link do produto na loja virtual</strong>
                <input type="text" name="ecommerce_link" size="30" value="'.$consulta['registro']->ecommerce_link.'" id="title">
            </div>
          </div>';
          

}

//Adicionando a criação de taxonomias no hook
add_action( 'init', 'criaTaxDepartamentos', 0 );

//Criando duas taxonomiascreate two taxonomies, genres and writers for the post type "book"
function criaTaxDepartamentos() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Departamentos', 'taxonomy general name' ),
    'singular_name'       => _x( 'Departamento', 'taxonomy singular name' ),
    'search_items'        => __( 'Procurar Departamentos' ),
    'all_items'           => __( 'Todos os Departamentos' ),
    'parent_item'         => __( 'Parent Departamento' ),
    'parent_item_colon'   => __( 'Parent Departamento:' ),
    'edit_item'           => __( 'Editar Departamento' ), 
    'update_item'         => __( 'Atualizar Departamento' ),
    'add_new_item'        => __( 'Adicionar novo Departamento' ),
    'new_item_name'       => __( 'Novo nome de Departamento' ),
    'menu_name'           => __( 'Departamentos' )
  );    

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'departamento' )
  );

  register_taxonomy( 'departamento', 'produto', $args );

}

//Adicionando o tipo de produto
add_action( 'init', 'criaTaxTipo', 0 );

//Criando duas taxonomiascreate two taxonomies, genres and writers for the post type "book"
function criaTaxTipo() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Tipos de produto', 'taxonomy general name' ),
    'singular_name'       => _x( 'Tipo de produto', 'taxonomy singular name' ),
    'search_items'        => __( 'Procurar tipos de produto' ),
    'all_items'           => __( 'Todos os tipos de produto' ),
    'parent_item'         => __( 'Parent tipo de produto' ),
    'parent_item_colon'   => __( 'Parent tipo de produto:' ),
    'edit_item'           => __( 'Editar tipo de produto' ), 
    'update_item'         => __( 'Atualizar tipo de produto' ),
    'add_new_item'        => __( 'Adicionar novo tipo de produto' ),
    'new_item_name'       => __( 'Novo nome de tipo de produto' ),
    'menu_name'           => __( 'Tipos de produto' )
  );    

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'tipo-de-produto' )
  );

  register_taxonomy( 'tipo-de-produto', 'produto', $args );

}

//Adicionando a criação de taxonomias no hook
add_action( 'init', 'criaTaxFabrica', 0 );

//Criando duas taxonomiascreate two taxonomies, genres and writers for the post type "book"
function criaTaxFabrica() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Fábricas', 'taxonomy general name' ),
    'singular_name'       => _x( 'Fábrica', 'taxonomy singular name' ),
    'search_items'        => __( 'Procurar Fábricas' ),
    'all_items'           => __( 'Todos as Fábricas' ),
    'parent_item'         => __( 'Parent Fábrica' ),
    'parent_item_colon'   => __( 'Parent Fábrica:' ),
    'edit_item'           => __( 'Editar Fábrica' ), 
    'update_item'         => __( 'Atualizar Fábrica' ),
    'add_new_item'        => __( 'Adicionar nova Fabricá' ),
    'new_item_name'       => __( 'Novo nome de Fabricá' ),
    'menu_name'           => __( 'Fabricás' )
  );    

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'fabrica' )
  );

  register_taxonomy( 'fabrica', 'produto', $args );

}
//Retorna há quanto tempo ocorreu determinada ação
	function hm_time_left($integer, $string="há %s") {  

		$seconds=$integer;  

     		if ($seconds/60 >=1) : // minuto ou mais
     			$minutes=floor($seconds/60);  
     		if ($minutes/60 >= 1):  // hora ou mais  
  		   	$hours=floor($minutes/60);  
  		if ($hours/24 >= 1):   // dia ou mais
  			$days=floor($hours/24);  
  				if ($days/7 >= 1):
  					$semanas = floor($days/7);
  				if ($days/30 >= 1):
  					$meses = floor($days/30);
  						if ($meses >=2): 
     							$return="$meses meses"; 
  						elseif ($meses ==1):
							$return="$meses mes"; 
						endif;
				else:
					if ($semanas >=2): 
     			 			$return="$semanas semanas"; 
  					elseif ($semanas ==1):
						$return="$semanas semana"; 
				endif;
		endif;
  		else:
  			if ($days >=2): 
     			 	$return="$days dias"; 
  		    	elseif ($days ==1):
		    		$return="$days dia"; 
			endif;
		endif;
		else:  
			if ($hours >=2):
				$return="$hours horas"; 
  			elseif ($hours ==1):
  			 	$return="$hours hora"; 
  			endif;
		endif;  				
  		else:
  			if ($minutes >=2):
				$return="$minutes minutos"; 
		    	elseif ($minutes ==1):
				$return="$minutes minuto"; 
  			endif;
  		endif;
  		else:
   			$return="alguns segundos"; 
  		endif;
			
				return str_replace('%s', $return, $string);  
	}


// Adicionando filtro para taxonomias na página de listagem de produtos
add_action( 'restrict_manage_posts', 'filtro_tipo_produto' );
function filtro_tipo_produto() {
    global $typenow;
    $taxonomy = 'tipo-de-produto'; // Change this
    if( $typenow != "page" && $typenow != "post" ){
        $filters = array($taxonomy);
        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms($tax_slug);
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) { 
                $label = (isset($_GET[$tax_slug])) ? $_GET[$tax_slug] : ''; // Fix
                echo '<option value='. $term->slug, $label == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            echo "</select>";
        }
    }
}

add_action( 'restrict_manage_posts', 'filtro_fabrica' );
function filtro_fabrica() {
    global $typenow;
    $taxonomy = 'fabrica'; // Change this
    if( $typenow != "page" && $typenow != "post" ){
        $filters = array($taxonomy);
        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms($tax_slug);
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) { 
                $label = (isset($_GET[$tax_slug])) ? $_GET[$tax_slug] : ''; // Fix
                echo '<option value='. $term->slug, $label == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            echo "</select>";
        }
    }
}

add_action( 'restrict_manage_posts', 'filtro_departamento' );
function filtro_departamento() {
    global $typenow;
    $taxonomy = 'departamento'; // Change this
    if( $typenow != "page" && $typenow != "post" ){
        $filters = array($taxonomy);
        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            $terms = get_terms($tax_slug);
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) { 
                $label = (isset($_GET[$tax_slug])) ? $_GET[$tax_slug] : ''; // Fix
                echo '<option value='. $term->slug, $label == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            echo "</select>";
        }
    }
}

/*// Register the column
function fabrica_column_register( $columns ) {
    $columns['fabrica'] = __( 'Fabrica', 'novo_ambiente_core' );

    return $columns;
}
add_filter( 'manage_edit-post_columns', 'fabrica_column_register' );
// Display the column content
function fabrica_column_display( $column_name, $post_id ) {
    if ( 'fabrica' != $column_name )
        return;

    $price = get_post_meta($post_id, 'fabrica', true);
    if ( !$price )
        $price = '<em>' . __( 'undefined', 'novo_ambiente_core' ) . '</em>';

    echo $price;
}
add_action( 'manage_posts_custom_column', 'fabrica_column_display', 10, 2 );
// Register the column as sortable
function fabrica_column_register_sortable( $columns ) {
    $columns['fabrica'] = 'fabrica';

    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'fabrica_column_register_sortable' );
function fabrica_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'fabrica' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'fabrica',
            'orderby' => 'meta_value_num'
        ) );
    }
 
    return $vars;
}
add_filter( 'request', 'fabrica_column_orderby' );*/
