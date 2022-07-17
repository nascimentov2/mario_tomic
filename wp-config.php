<?php

/**
 * The base configurations of the WordPress.
 *
 * This file is a custom version of the wp-config file to help
 * with setting it up for multiple environments. Inspired by
 * Leevi Grahams ExpressionEngine Config Bootstrap
 * (http://ee-garage.com/nsm-config-bootstrap)
 *
 * @package WordPress
 * @author Abban Dunne @abbandunne
 * @link http://abandon.ie/wordpress-configuration-for-multiple-environments
 */

// Update this according to your needs
define('TIMESTART', microtime(true));

$environment_map = array(
	'www' => 'development',
	'www.homemmaquina.com.br' => 'labs',
	'labs.homemmaquina.com.br' => 'labs',
	'www.novoambiente.com.br' => 'production',
	'www.novoambiente.dreamhosters.com' => 'production',
	'design.novoambiente.com' => 'production',
	'minim.in' => 'mini'
);

// Get Server name
$server_name = $_SERVER['SERVER_NAME'];

define('ENVIRONMENT', $environment_map[$server_name]);


define('ABSPATH', dirname(__FILE__) . '/');

$env_config = ABSPATH . sprintf('wp-config-%s.php', ENVIRONMENT);

// Terminate if file doesn't exist
// if(!file_exists(($env_config)))	exit("$env_config doesn't exist");

/** Sets up WP config based on environment. */
require($env_config);

?>