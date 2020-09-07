<?php
	add_action( 'wp_enqueue_scripts', 'acme_child_enqueue_styles' );
	function acme_child_enqueue_styles() {
		wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'acme_main-styles' ), '1.0.0','all' );
	}

	function my_favicon_link() {
		echo '<link rel="shortcut icon" type="image/x-icon" href="'. get_stylesheet_directory_uri().'/favicon.ico' .'" />' . "\n";
	}
	add_action( 'wp_head', 'my_favicon_link' );