<?php
	add_action( 'wp_enqueue_scripts', 'acme_child_enqueue_styles' );
	function acme_child_enqueue_styles() {
		wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'acme_main-styles' ), '1.0.0','all' );
	}

	function my_favicon_link() {
		echo '<link rel="shortcut icon" type="image/x-icon" href="'. get_stylesheet_directory_uri().'/favicon.ico' .'" />' . "\n";
	}
	add_action( 'wp_head', 'my_favicon_link' );

	function acme_register_faculty_post_type() {
		$labels = array(
			'name'                  => 'Faculties',
			'singular_name'         => 'Faculty',
			'menu_name'             => 'Faculty',
			'name_admin_bar'        => 'Faculty',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New Faculty',
			'new_item'              => 'New Faculty',
			'edit_item'             => 'Edit Faculty',
			'view_item'             => 'View Faculty',
			'all_items'             => 'All Faculty',
			'search_items'          => 'Search Faculty',
			'parent_item_colon'     => 'Parent Faculty:',
			'not_found'             => 'No faculties found.',
			'not_found_in_trash'    => 'No faculties found in Trash.',
			'featured_image'        => 'Faculty Profile Image',
			'set_featured_image'    => 'Set Faculty Image',
			'remove_featured_image' => 'Remove Faculty Image',
			'use_featured_image'    => 'Use as Faculty Image',
			'archives'              => 'Faculty archives',
			'insert_into_item'      => 'Insert into faculty',
			'uploaded_to_this_item' => 'Uploaded to this faculty',
			'filter_items_list'     => 'Filter faculties list',
			'items_list_navigation' => 'Faculties list navigation',
			'items_list'            => 'Faculties list',
		);
	 
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'faculty' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);
	 
		register_post_type( 'faculty', $args );
	}

	function acme_register_faculty_taxonomy() {

		$labels = array(
			'name'              => 'Department',
			'singular_name'     => 'Department',
			'search_items'      => 'Search Department',
			'all_items'         => 'All Departments',
			'parent_item'       => 'Parent Department',
			'parent_item_colon' => 'Parent Department:',
			'edit_item'         => 'Edit Department',
			'update_item'       => 'Update Department',
			'add_new_item'      => 'Add New Department',
			'new_item_name'     => 'New Department Name',
			'menu_name'         => 'Department',
		);

		register_taxonomy( 'department', 'faculty', array(
			'label'        		=> 'Department',
			'labels'       		=> $labels,
			'rewrite'      		=> array( 'slug' => 'department' ),
			'hierarchical' 		=> true,
			'show_ui'           => true,
        	'show_admin_column' => true,
        	'query_var'         => true,
			'update_count_callback' => '_update_post_term_count',
		) );
	}
	add_action( 'init', 'acme_register_faculty_taxonomy');
	 
	add_action( 'init', 'acme_register_faculty_post_type' );


	add_action( 'after_switch_theme', 'my_rewrite_flush' );
	function my_rewrite_flush() {
		acme_register_faculty_post_type();
		acme_register_faculty_taxonomy();
		flush_rewrite_rules();
	}


	function faculty_add_metabox(){

			add_meta_box(
				'faculty_metabox',
				'Faculty Details',
				'faculty_metabox_html',
				'faculty',
				'normal',
				'high'
			);
	}
	add_action('add_meta_boxes', 'faculty_add_metabox');

	function faculty_metabox_html(){
		global $post;

		$values = get_post_custom( $post->ID );
		$f_designation = isset( $values['faculty_designation'] ) ? esc_attr( $values['faculty_designation'][0] ) : "";
		$f_contact = isset( $values['faculty_contact'] ) ? esc_attr( $values['faculty_contact'][0] ) : "";

		wp_nonce_field( 'faculty_metabox_nonce', 'faculty_meta_box_nonce' );
		?>
		<p>
			<label for="faculty_designation">Designation</label>
			<input type="text" name="faculty_designation" id="faculty_designation" value="<?php echo $f_designation; ?>"/>
		</p>
		<p>
			<label for="faculty_contact">Contact</label>
			<input type="text" name="faculty_contact" id="faculty_contact" value="<?php echo $f_contact; ?>"/>
		</p>
		<?php
	}


	function faculty_metabox_save( $post_id ){

		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		if( !isset( $_POST['faculty_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['faculty_meta_box_nonce'], 'faculty_metabox_nonce' ) ) return;

		if( !current_user_can( 'edit_post' ) ) return;

		if( isset( $_POST['faculty_designation'] ) )
			update_post_meta( $post_id, 'faculty_designation', esc_attr( $_POST['faculty_designation'] ) );

		if( isset( $_POST['faculty_contact'] ) )
			update_post_meta( $post_id, 'faculty_contact', esc_attr( $_POST['faculty_contact'] ) );

	}
	add_action( 'save_post', 'faculty_metabox_save' );