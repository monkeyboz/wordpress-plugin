<?php
    /*
    Plugin Name: RSS Feed System
    Plugin URI: http://www.taureanwooley.com
    Description: Plugin that allows for uploading rss-feeds into wordpress
    Author: Taurean Wooley
    Version: 1.0
    Author URI: http://www.taureanwooley.com
    */
    include_once('ttp-admin.php');
    if (!function_exists('ttp_admin_actions')) {
        function ttp_admin_actions() {
			add_menu_page('RSS Feed Importer', 'RSS Feed Importers', 'administrator', __FILE__, 'ttp_admin',plugins_url('Local-Splash-Med-600x200.jpg', __FILE__));
        }
        add_action('admin_menu', 'ttp_admin_actions');
        
        function register_new_post_type(){
        	$array = array(
        			"label"	=>	"Feeds",
        			"public"	=>	true,
			        'show_ui' => true,
			        '_builtin' => false,
			        '_edit_link' => 'post.php?post=%d',
			        'capability_type' => 'feeds',
			        'hierarchical' => false,
			        'rewrite' => array("slug" => "feeds"),
			        'query_var' => "feeds",
        			"supports"	=>	array("title","editor","thumbnail","excerpt"),
        		);
        	register_post_type('feeds',$array);
        }
       	add_action('init','register_new_post_type');
       	
       	function save_feed_meta( $post_id, $post, $update ) { /*
            * In production code, $slug should be set only once in the plugin,
            * preferably as a class property, rather than in each function that needs it.
            */
            $slug = 'feed';
            
            // If this isn't a 'book' post, don't update it.
            if ( $slug != $post->post_type ) {
                return;
            }
            
            // - Update the post's metadata.
            
            if ( isset( $_REQUEST['book_author'] ) ) {
                update_post_meta( $post_id, 'book_author', sanitize_text_field( $_REQUEST['book_author'] ) );
            }
            
            if ( isset( $_REQUEST['publisher'] ) ) {
                update_post_meta( $post_id, 'publisher', sanitize_text_field( $_REQUEST['publisher'] ) );
            }
            
            // Checkboxes are present if checked, absent if not.
            if ( isset( $_REQUEST['inprint'] ) ) {
                update_post_meta( $post_id, 'inprint', TRUE );
            } else {
                update_post_meta( $post_id, 'inprint', FALSE );
            }
        }
        add_action( 'save_post', 'save_feed_meta', 10, 3 );
        
        function feed_searches(){
        	$layout = new Layout(array('post'=>'feeds'));
            $query = new WP_Query('post_type=feeds');
            $layout->get_layout(plugin_dir_path( __FILE__  ).'/layout/layout.php');
           	
        	$string .= $layout->populate_layout($query->posts);
            return $string;
        }
        add_shortcode('feed_searches','feed_searches');
        
        function feed_info($id){
            $feed = new WP_Query('post_type=feeds&ID='.$id);
            print_r($feed);
            //include_once('ttp-import-admin.php'); 
        }
        add_shortcode('feed_info','feed_info');
        
        function ttp_admin() {
            include_once('ttp-import-admin.php');
        }
    }
?>