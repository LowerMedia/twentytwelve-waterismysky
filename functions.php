<?php

/*
#
#   REGISTER JS AND CSS
#
*/

    // function lowermedia_scripts() {
        // wp_enqueue_script(
        //     'custom-js',
        //     get_stylesheet_directory_uri() . '/custom.js',
        //     array( 'jquery' )
        // );

        // if (is_front_page()) {
        //     wp_enqueue_script(
        //         'jssor',
        //         get_stylesheet_directory_uri() . '/js/jssor.js',
        //         array( 'jquery' )
        //     );

        //     wp_enqueue_script(
        //         'jssorslider',
        //         get_stylesheet_directory_uri() . '/js/jssor.slider.js',
        //         array( 'jquery' )
        //     );
        // }

        // wp_enqueue_script('jquery-ui-accordion');
    // }
    // add_action( 'wp_enqueue_scripts', 'lowermedia_scripts' );

    // function lowermedia_enqueue_parent_style() {
    //     wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    // }
    // add_action( 'wp_enqueue_scripts', 'lowermedia_enqueue_parent_style' );


/*
#
#   ADD CUSTOM CONTENT TYPES 
#
*/

    /**
     * Custom Post Types, on the fly creation
     *
     **/

    function lm_custom_post_type_creator($post_type_name, $description, $public, $menu_position, $supports, $has_archive, $irreg_plural) {
      if ($irreg_plural) {$plural = 's';} else {$plural = '';}
      $labels = array(
        'name'               => _x( $post_type_name, 'post type general name' ),
        'singular_name'      => _x( strtolower($post_type_name), 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'book' ),
        'add_new_item'       => __( 'Add New '.$post_type_name),
        'edit_item'          => __( 'Edit '.$post_type_name ),
        'new_item'           => __( 'New '.$post_type_name ),
        'all_items'          => __( 'All '.$post_type_name.$plural ),
        'view_item'          => __( 'View '.$post_type_name ),
        'search_items'       => __( 'Search'.$post_type_name.$plural ),
        'not_found'          => __( 'No '.$post_type_name.$plural.' found' ),
        'not_found_in_trash' => __( 'No '.$post_type_name.$plural.' found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => $post_type_name
      );
      $args = array(
        'labels'        => $labels,
        'description'   => $description,
        'public'        => $public,
        'menu_position' => $menu_position,
        'supports'      => $supports,
        'has_archive'   => $has_archive,
      );
      register_post_type( $post_type_name, $args ); 
    }
    add_action( 'init', lm_custom_post_type_creator('Press', 'Holds our press articles', true, 4, array( 'title', 'editor', 'thumbnail' ), true, false));
    add_action( 'init', lm_custom_post_type_creator('Staff', 'Holds our staff specific data', true, 5, array( 'title', 'editor', 'thumbnail' ), true, false));
    // add_action( 'init', lm_custom_post_type_creator('Car Care Tips', 'Holds our car care tips.', true, 6, array( 'title', 'editor', 'thumbnail', 'excerpt' ), true, false));
    // add_action( 'init', lm_custom_post_type_creator('Car Care Videos', 'Holds our car care videos.', true, 7, array( 'title', 'editor', 'thumbnail' ), true, false));

    //change slug for staff
    register_post_type(
        'press',
        array(
            'labels' => array(
                'name' => 'Press Articles',
                'singular_name' => 'Press Article'
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'press-article'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'can_export' => true,
        )
    );

    //change slug for staff
    register_post_type(
        'staff',
        array(
            'labels' => array(
                'name' => 'Crew',
                'singular_name' => 'Crew Member'
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'crew'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'can_export' => true,
        )
    );

    /**
     * Adds a box to the main column on the Post and Page edit screens.
     */
    function lm_add_meta_box() {
        add_meta_box(
            'lowermedia-staff-sitelink',
            __( 'Staff Website Link', 'lm_textdomain' ),
            'lm_meta_box_callback',
            'staff',//$screen
            'side',
            'high'
        );

        add_meta_box(
            'lowermedia-press-link',
            __( 'Press Link', 'lm_textdomain' ),
            'lm_meta_box_callback',
            'press',//$screen
            'side',
            'high'
        );
    }
    add_action( 'add_meta_boxes', 'lm_add_meta_box' );

    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    function lm_meta_box_callback( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'lm_meta_box', 'lm_meta_box_nonce' );

        
        // * Use get_post_meta() to retrieve an existing value
        // * from the database and use the value for the form.
         
        $value = get_post_meta( $post->ID, '_lm_meta_value_key', true );

        echo '<label for="lm_new_field">';
        _e( '', 'lm_textdomain' );
        echo '</label> ';
        echo '<input type="text" id="lm_new_field" name="lm_new_field" value="' . esc_attr( $value ) . '" size="25" />';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
     function lm_save_meta_box_data( $post_id ) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['lm_meta_box_nonce'] ) ) { return; }
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['lm_meta_box_nonce'], 'lm_meta_box' ) ) { return; }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) { if ( ! current_user_can( 'edit_page', $post_id ) ) { return; }
        } else { if ( ! current_user_can( 'edit_post', $post_id ) ) { return; } }

        /* OK, it's safe for us to save the data now. */
        // Make sure that it is set.
        if ( ! isset( $_POST['lm_new_field'] ) ) { return;  }
        // Sanitize user input.
        $my_data = sanitize_text_field( $_POST['lm_new_field'] );
        // Update the meta field in the database.
        update_post_meta( $post_id, '_lm_meta_value_key', $my_data );
    }
    add_action( 'save_post', 'lm_save_meta_box_data' );



/*
#
#   Make Archives.php Include Custom Post Types
#   http://css-tricks.com/snippets/wordpress/make-archives-php-include-custom-post-types/
#
*/
/*

    --    ADD CUSTOM POST TYPES HERE   --

*/
    function namespace_add_custom_types( $query ) {
      if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
            $query->set( 'post_type', array( 'post', 'post-type-name' ));
            return $query;
        }
    }
    add_filter( 'pre_get_posts', 'namespace_add_custom_types' );

/*
#
#   Define what post types to search
#   The hook needed to search ALL content
#
*/
/*

    --    ADD CUSTOM POST TYPES HERE   --

*/
    function searchAll( $query ) {
        if ( $query->is_search ) {
            $query->set( 'post_type', array( 'post', 'page', 'feed', 'products', 'people'));
        }
        return $query;
    }
    add_filter( 'the_search_query', 'searchAll' );

/*
#
#   REGISTER SIDEBARS/WIDGET AREAS
#   
#
*/

    function lowermedia_widgets_init() {

        // register_sidebar( array(
        //     'name' => 'Pre Content Widget Area',
        //     'id' => 'pre-content-widget',
        //     'before_widget' => '<div id="pre-content-widget" class="pre-content-widget">',
        //     'after_widget' => '</div>',
        //     'before_title' => '<h2 class="rounded">',
        //     'after_title' => '</h2>',
        // ) );

    }
    add_action( 'widgets_init', 'lowermedia_widgets_init' );

/*
#
#   REGISTER MENUS
#   
#
*/

    function lowermedia_menus_init() {
      register_nav_menus(
        array(
          'header-social-media' => __( 'Header Social Media Menu' )
        )
      );
    }
    add_action( 'init', 'lowermedia_menus_init' );


/*
#   Create widget info for above function: lm_add_dashboard_widgets
*/
    function lm_theme_info() {
      echo "
          <ul>
          <li><strong>Developed By:</strong> LowerMedia.Net</li>
          <li><strong>Website:</strong> <a href='http://lowermedia.net'>www.lowermedia.net</a></li>
          <li><strong>Contact:</strong> <a href='mailto:pete.lower@gmail.com'>pete.lower@gmail.com</a></li>
          </ul>"
      ;
    }

/*
#
#   ENABLE SHORTCODE IN WIDGETS
#
*/
    add_filter('widget_text', 'do_shortcode');
/*
#
#   CONTACT FORM  FUNCTION
#   - changing default WordPress email settings
*/
    function lowermedia_mail_from($old) { return 'waterismysky@gmail.com'; }
    add_filter('wp_mail_from', 'lowermedia_mail_from');
    function lowermedia_mail_from_name($old) { return 'Water Is My Sky'; }
    add_filter('wp_mail_from_name', 'lowermedia_mail_from_name');

/*
# SPEED OPTIMIZATIONS
# 
*/

// Remove jquery migrate as is not needed
if(!is_admin()) add_filter( 'wp_default_scripts', 'dequeue_jquery_migrate' );
function dequeue_jquery_migrate( &$scripts){
    $scripts->remove( 'jquery');
    $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
}

//load jquery from google
if (!is_admin()) add_action("wp_enqueue_scripts", "lowermedia_jquery_enqueue", 11);
function lowermedia_jquery_enqueue() {
    wp_deregister_script('jquery');
    // wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null, true);
    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js", false, null, true);
    wp_enqueue_script('jquery');
}

/*
#
#   SPEED OPTIMIZATIONS
#   -Load all fonts from google
#
#
*/

    function load_fonts() {
        wp_dequeue_style( 'twentytwelve-fonts' );
        wp_deregister_style( 'twentytwelve-fonts' );
        wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Lato:400,900|Kite+One|Signika:400,700|Open+Sans:400italic,700italic,400,700');
        wp_enqueue_style( 'googleFonts');
    }
    add_action('wp_print_styles', 'load_fonts');

/*
#
#   END
#
*/