<?php
/**
 * MU Starter Plugin
 *
 * This is a starter plugin for Marshall University's WordPress network.
 *
 * @package  MU Starter Plugin
 *
 * Plugin Name:  MU Starter Plugin
 * Plugin URI: https://www.marshall.edu
 * Description: This is a starter plugin for Marshall University's WordPress network.
 * Version: 1.0
 * Author: Marshall University
 */

/**
 * Proper way to enqueue scripts and styles
 */
function mu_starter_plugin_scripts() {
	wp_enqueue_style( 'mu-starter-plugin', plugin_dir_path( __FILE__ ) . 'css/mu-starter-plugin.css', '', true );
}
add_action( 'wp_enqueue_scripts', 'mu_starter_plugin_scripts' );
