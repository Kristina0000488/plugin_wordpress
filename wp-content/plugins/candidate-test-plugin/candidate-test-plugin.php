<?php
/*
Plugin Name: Candidate Test Plugin.
Description: Candidate Test Plugin for the test task.
Author: K.
*/

function themeslug_enqueue_style() {
   wp_enqueue_style( 'ctp-style-css', plugins_url('includes/ctp-style.css', __FILE__ ) );
}
    
function themeslug_enqueue_script() {
    wp_enqueue_script( 'ctp-scripts-js', plugins_url('includes/ctp-scripts.js', __FILE__ ), array( 'jquery' ) );
}
    
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );

require_once plugin_dir_path(__FILE__) . 'includes/ctp-functions.php';

