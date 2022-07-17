<?php 

// ============================================
//	Adiciona thumbnails ao tema
// ============================================

if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' );
}


// ============================================
//	Adiciona thumbnails ao tema
// ============================================

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'mini', 60, 60 ); // Thumbnails mini
	add_image_size( 'thumb-home', 210, 210 ); // Thumbnails da home para artigos do blog
	add_image_size( 'foto-galeria', 768, 768 ); // Thumbnails da home para artigos do blog	
}

$template_file = dirname(__FILE__)."/../../uploads/script_cache/thumbnail-cache.json";
$debug_file = dirname(__FILE__)."/../../uploads/script_cache/debug-cache.txt";

function set_thumbnail_cache( $idpost, $url ){

	global $template_file;
	
	if (is_file($template_file)):

          $data = file_get_contents($template_file);
          $backup = json_decode($data, true);
          
	else:

		$backup = array();

	endif;

	if (is_numeric($idpost)):

		$backup[$idpost] = $url;
		$handle = fopen ($template_file, 'w+');
          fwrite($handle, json_encode($backup));
          fclose($handle);
		  
		  	$debug_file = dirname(__FILE__)."/../../uploads/script_cache/debug-cache.txt";
			$handle_txt = fopen ($debug_file, 'w+'); 
		 	fwrite($handle_txt, $idpost.'-');
          	fclose($handle_txt);
		  
     	return true;

     else:
		
		/** $debug_file = dirname(__FILE__)."/../../uploads/script_cache/debug-cache.txt";
		$handle = fopen ($debug_file, 'w+'); 
		 fwrite($handle, $idpost);
          fclose($handle); */ 
		 
		 
     	return false;
	endif;

}

function get_thumbnail_cache(){

	global $template_file;

	if (is_file($template_file)):

          $data = file_get_contents($template_file);
          $backup = json_decode($data, true);
          return $backup;

	else:

		return array();

	endif;

}

function get_excerpt_by_obj($the_post){
    
    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $excerpt_length = 35; //Sets excerpt length by word count
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if(count($words) > $excerpt_length) :
        array_pop($words);
        array_push($words, '…');
        $the_excerpt = implode(' ', $words);
    endif;

    $the_excerpt = '<p>' . $the_excerpt . '</p>';

    return $the_excerpt;
}

// ============================================
//	Adiciona menus
// ============================================

	// register_nav_menu( 'main', 'Menu Principal' );

function the_breadcrumb($id) {
	if (!is_home()) {
		echo '<a href="';
		echo get_option('home');
		echo '">';
		echo "Cat&aacute;logo</a> » ";
		if (is_category() || is_single()) {
			$departamentos = wp_get_post_terms($id, 'departamento', array("fields" => "all"));
			$tipoproduto = wp_get_post_terms($id, 'tipo-de-produto', array("fields" => "all"));
			
			if ($tipoproduto[0]->name != $departamentos[0]->name):
				if (count($departamentos) > 0):
					echo "<a href='".BASE_URL.$departamentos[0]->taxonomy."/".$departamentos[0]->slug."'>".$departamentos[0]->name.'</a> » ';
				endif;
			endif;
			
			if (count($tipoproduto) > 0):
				echo "<a href='".BASE_URL.$tipoproduto[0]->taxonomy."/".$tipoproduto[0]->slug."'>".$tipoproduto[0]->name.'</a> » ';
			endif;

			if (is_single()) {
			the_title();
			}
		} elseif (is_page()) {
			echo the_title();
		}
	}
}

// ============================================
//	Removendo jQuery da Home
// ============================================

if( !is_admin()){ 
	
function mytheme_enqueue_scripts() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', ("http://cdn.jquerytools.org/1.1.2/jquery.tools.min.js"), false, '1.3.2');
	//wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"), false, '1.7.2');
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

}


function ST4_get_featured_image($post_ID) {  
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);  
    if ($post_thumbnail_id) {  
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'mini');  
        return $post_thumbnail_img[0];  
    }  
}

// ADD NEW COLUMN  
function ST4_columns_head($defaults) {  
    $defaults['featured_image'] = 'Imagem destacada';  
    return $defaults;  
}  
  
// SHOW THE FEATURED IMAGE  
function ST4_columns_content($column_name, $post_ID) {  
    if ($column_name == 'featured_image') {  
        $post_featured_image = ST4_get_featured_image($post_ID);  
        if ($post_featured_image) {  
            echo '<img src="' . $post_featured_image . '" style="width:60px;height:60px" />';  
        } 
        else {  
            // NO FEATURED IMAGE, SHOW THE DEFAULT ONE  
            echo '<img src="' . get_bloginfo( 'template_url' ) . '/images/default.jpg" />';  
        } 
    }  
}

add_filter('manage_posts_columns', 'ST4_columns_head');  
add_action('manage_posts_custom_column', 'ST4_columns_content', 10, 2); 

?>