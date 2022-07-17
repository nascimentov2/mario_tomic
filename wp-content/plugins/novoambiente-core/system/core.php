<?php
/**
 * core
 * @description Carrega os controllers e models do sistema, além de carregar as configurações
 * utilizadas em views e classes
 * 
 * @author Homem Máquina
 * @package Novo Ambiente
 */

//Definição de constantes

define('PATH_SITE', dirname(__FILE__).'/../../../../');
define('PATH_PLUGIN', dirname(__FILE__).'/../');
define('DB_PREFIX', 'na_sys');
define('DB_VARIACOES', DB_PREFIX.'_produtos_variacoes');
define('DB_PRODUTOS', DB_PREFIX.'_produtos');
define('DB_DESIGNERS', DB_PREFIX.'_designers');
define('DB_FABRICAS', DB_PREFIX.'_fabricas');
define('DB_ACABAMENTOS', DB_PREFIX.'_acabamentos');
define('DB_ACABAMENTOS_TIPOS', DB_PREFIX.'_acabamentos_tipos');
define('DB_ACABAMENTOS_GRUPOS', DB_PREFIX.'_acabamentos_grupos');
define('DB_VALORES', DB_PREFIX.'_valores');
define('DB_USUARIOS', DB_PREFIX.'_usuarios');
define('DB_CIDADES', DB_PREFIX.'_cidades');
define('DB_REGISTROS', DB_PREFIX.'_registros');
define('DB_PEDIDOS', DB_PREFIX.'_pedidos');
define('DB_IMG_QUEUE', DB_PREFIX.'_img_queue');

define('EMAIL_LOGO', BASE_URL.'/logo.jpg');

//@TODO tornar a detecção dinamica, o wp_upload_dir() não funcionou
$dir = PATH_SITE.'wp-content/uploads';

//Caminho em que os arquivos temporarios (planilhas de Excel) são salvos
define('PATH_SAVE_TEMP_FILES', $dir.'/temp_files/');
 

//Functions padrão
include('functions.php');

//Arquivos de configuração 
/*foreach (glob(PATH_PLUGIN.'app/config/*.config.php') as $value):
 include_once($value);
endforeach;*/

//Classes padrão
 include(PATH_PLUGIN.'system/core/Database.class.php');
 include(PATH_PLUGIN.'system/core/Sql.class.php');

//Bibliotecas do sistema
 include(PATH_PLUGIN.'app/libs/excel_reader.lib.php');
 include(PATH_PLUGIN.'app/libs/tcpdf/tcpdf.php');

//Classes de sistema
 include(PATH_PLUGIN.'app/model/Registros.class.php');
 include(PATH_PLUGIN.'app/model/Usuarios.class.php');

//Controllers de sistema
 include(PATH_PLUGIN.'app/controller/Registro.controller.php');
 include(PATH_PLUGIN.'app/controller/Usuario.controller.php');
    
