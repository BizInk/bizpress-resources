<?php
/**
 * Plugin Name: BizPress Resources
 * Description: Display business content on your website that is automatically updated by the Bizink team.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.0
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Text Domain: bizink-resources
 * Domain Path: /languages
 * Requires Plugins: bizpress-client
 */

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Updater
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker('https://github.com/BizInk/bizpress-resources',__FILE__,'bizpress-resources');
$myUpdateChecker->setBranch('main');
$myUpdateChecker->setAuthentication('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');

if(is_plugin_active("bizpress-client/bizink-client.php")){
	require_once 'resources.php';
}