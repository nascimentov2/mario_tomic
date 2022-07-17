<?php
	//print_r($_GET); die;
	if( !isset($_SESSION) ){ session_start(); }

	require ("../../../../wp-config.php");
	//include ("core.php");
	require ("../../../../wp-load.php");
	//require ("../../../../wp-includes/wp-db.php");

	if (!function_exists('wp_generate_attachment_metadata')):
		require ( ABSPATH . 'wp-admin/includes/image.php' );
    	endif;

	/** Doing the connection */
	if (!defined('CONN'))
		define("CONN", mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) );
	mysql_select_db( DB_NAME, CONN );

	//$database = new database;
		$class  = $_GET["class"];
		$method = $_GET["func"];
		$format	= ( isset($_GET["format"]) ) ? $_GET["format"] : "default";
						
		if(!class_exists($class)):
			die("<h1>Erro interno</h1>");
		elseif(!method_exists($class, $method)):
			die("<h1>Erro interno</h1>");
		else:
			$instance = new $class;
			$data = $instance->$method($_POST);
			$globalRedirect = ( isset($_SESSION["globalRedirectUrl"]) ) ? $_SESSION["globalRedirectUrl"] : "";
			unset($_SESSION["globalRedirectUrl"]);
			//$database->closeConnection();
			
			if( $format != "default" ):
				switch( $format ):
					case "json":
						echo json_encode($data);
						break;
					
					default:
						echo "error";
						break;
				endswitch;
			else:
				header("location: ".BASE_URL.$globalRedirect);
				die();
			endif;
		endif; 

?>
