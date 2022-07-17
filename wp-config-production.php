<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */


define('BASE_URL', 'http://design.novoambiente.com/');

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'DB NAME');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'DB USER');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'PASSWORD HERE');

/** nome do host do MySQL */
define('DB_HOST', 'mysql.novoambiente.dreamhosters.com');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '}}zLk|nCrX=BRR=8,0q&Yy|:o$,^]/:UKe_w+vreI%4k2bn@62J&@NGZrp@s$E+r');
define('SECURE_AUTH_KEY',  '8Rd7DM=i>xcnFTqqMxw`dsWtG5v]ak9`/I!C L.3@!p64$RP?~a G|v$,y#!iqm:');
define('LOGGED_IN_KEY',    'm}BXMI(s[=fBW$SrKU& zA((9ZHoetglk{799HV;MOnf9MyYthOQ}5#0e@8+FWc%');
define('NONCE_KEY',        '{QtFo|HiUZ5*`R9GD4nH.GA3<i/8~6zQDp$C3?IT<Lll<fZ[0.cf<CwF%_QTqp{s');
define('AUTH_SALT',        'XrhVo?C)DL^r_;h-|8x*6o-V[x{#i80lx&N)6t,~s2`QOD`;djxmS67[1LT-bSS%');
define('SECURE_AUTH_SALT', '8dEBaHX#ah]2_.WiUX),|?f,I%OF,g&)QM-HdQL5dVod[9nT-OrZ4tYFFd{&tQ0y');
define('LOGGED_IN_SALT',   'U)L%sn}FO4ycPL~#I/YhYxjk3{[16En*g,V^dO*>qb?tPk>k#}!7TF!p%T.m0|]l');
define('NONCE_SALT',       'T*z-e3d5c<RsQs1 &hd}}AMS9ev,#(1qQRF5>:j5V[<x0vwHcqx,AJ:zr~7rD?x_');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'na_tb_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');
define( 'WP_MEMORY_LIMIT','128M' );
define( 'WP_MAX_MEMORY_LIMIT','512M' );
/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

ini_set('display_errors', false);

error_reporting(0);
/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
