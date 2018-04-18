<?php

function chownow_load_scripts() {
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), NULL, 'all');
	wp_enqueue_style('custom', get_template_directory_uri() . '/css/style.css', array(), NULL, 'all');
	wp_enqueue_style('lato', 'https://fonts.googleapis.com/css?family=Lato');
	wp_enqueue_style('cata', 'https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900');
	wp_enqueue_style('muli', 'https://fonts.googleapis.com/css?family=Muli');
	
	wp_enqueue_script('jquerys', get_template_directory_uri() . '/js/jquery.min.js', array(), '', true);
	wp_enqueue_script('bundle', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array(), '', true);
	wp_enqueue_script('easing', get_template_directory_uri() . '/js/jquery.easing.min.js', array(), '', true);
	wp_enqueue_script('customjs', get_template_directory_uri() . '/js/new-age.js', array(), '', true);

}

add_action('wp_enqueue_scripts', 'chownow_load_scripts');