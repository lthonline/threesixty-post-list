<?php

class Threesixty_Post_List {
    
    private static $initiated = false;
    
    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
	}
    }
    
    private static function init_hooks() {        
        self::$initiated = true;
        add_action( 'wp_enqueue_scripts', array( 'Threesixty_Post_List', 'load_resources' ));        
        add_action('wp_ajax_filter_posts', array('Threesixty_Post_List', 'filter_posts'));
        add_action('wp_ajax_nopriv_filter_posts', array('Threesixty_Post_List', 'filter_posts'));
        add_shortcode( 'threesixty_post_list', array('Threesixty_Post_List', 'render_shortcode') );
    }
    
    private static function bail_on_activation($message, $deactivate = true) {
?>
        <!doctype html>
        <html>
        <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <style>
        * {
        	text-align: center;
        	margin: 0;
        	padding: 0;
        	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
        }
        p {
        	margin-top: 1em;
        	font-size: 18px;
        }
        </style>
        </head>
        <body>
        <p><?php echo esc_html($message); ?></p>
        </body>
        </html>
        <?php
        if ($deactivate) {
            $plugins = get_option('active_plugins');
            $threesixty = plugin_basename(THREESIXTY_POST_LIST___PLUGIN_DIR . 'threesixty-post-list.php');
            $update = false;
            foreach ($plugins as $i => $plugin) {
                if ($plugin === $threesixty) {
                    $plugins[$i] = false;
                    $update = true;
                }
            }

            if ($update) {
                update_option('active_plugins', array_filter($plugins));
            }
        }
        exit;
    }
    
    public static function plugin_activation() {
        if (version_compare($GLOBALS['wp_version'], THREESIXTY_POST_LIST___MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('threesixty-post-list');

            $message = '<strong>' . sprintf(esc_html__('Threesixty Post List %s requires WordPress %s or higher.', 'threesixty-post-list'), THREESIXTY_POST_LIST_VERSION, THREESIXTY_POST_LIST___MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version.', 'threesixty-post-list'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://codex.wordpress.org/Upgrading_WordPress');

            Threesixty_Post_List::bail_on_activation($message);
        } elseif (!empty($_SERVER['SCRIPT_NAME']) && false !== strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php')) {            
            
            global $wpdb;
            $threesixty_post_list_db_version = THREESIXTY_POST_LIST_DB_VERSION;
            $table_name = $wpdb->prefix . "threesixty_post_list";
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id int NOT NULL AUTO_INCREMENT,
                title varchar(250) NOT NULL,
                date_created datetime NULL,
                date_updated datetime NULL,                
                post_type varchar(250) NOT NULL,
                taxonomy_type varchar(100) NULL,
                item_per_page int NULL,
                is_active BOOLEAN,
                PRIMARY KEY id (id)
                ) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            add_option( 'threesixty_post_list_db_version', $threesixty_post_list_db_version );
        }     
    }
    
    public static function plugin_deactivation() {}
    
    public static function plugin_delete() {
        global $wpdb;
        $threesixty_post_list_db_version = THREESIXTY_POST_LIST_DB_VERSION;
        $table_name = $wpdb->prefix . "threesixty_post_list";
        
        $sql = "DROP TABLE IF EXISTS $table_name";
        
        $wpdb->query($sql);
        delete_option( 'threesixty_post_list_db_version', $threesixty_post_list_db_version );
    }
    
    public static function load_resources() {
        $pluginManiCSS = 'assets/css/main.css';
        $pluginMainJS = 'assets/js/main.js';
        
        wp_register_style( 'tpl-main', plugin_dir_url(__FILE__) . $pluginManiCSS , array(), filemtime(plugin_dir_path(__FILE__) . $pluginManiCSS));
	wp_enqueue_style( 'tpl-main');
        
        wp_register_script('tpl-main', plugin_dir_url(__FILE__) . $pluginMainJS , array(), filemtime(plugin_dir_path(__FILE__) . $pluginMainJS), true);
        wp_enqueue_script('tpl-main');        
    }
    
    public static function render_shortcode($atts = array(), $content = null, $tag = null){
                
        $args = shortcode_atts(array(
            'id' => 0
        ), $atts);
        
        $id = $args['id'];
        
        global $wpdb;
        $table_name = $wpdb->prefix. "threesixty_post_list";
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        
        if($result == null) {
            return '<p>Post list for id ' . $id . ' did not found.</p>';
        }
        
        $post_type = $result->post_type;
        $itemPerPage = $result->item_per_page;
        $taxonomy_type = $result->taxonomy_type;
        
        if(!$result->is_active) {
            return '<p>Post list for id ' . $id . ' is inactive, please check.</p>';
        }
        
        $term_args = array(
            'taxonomy' => $taxonomy_type,
            'hide_empty' => true,
        );

        $terms = get_terms($term_args);
                
        $post_args = array(
            'posts_per_page'   => $itemPerPage,
            'orderby'       => 'title',
            'post_type'     => $post_type
        );
        $posts = new WP_Query($post_args);
        
        $response = '';
        
        $response .= '<div class="threesixty-post-list">';
        
            if($terms) { 
                $response .= '<div class="row"><ul class="nav tax-nav">';
                    $response .= '<li class="nav-item tax-nav-item active"><a class="nav-link tax-nav-link" href="#!" data-slug="all" data-post_type="' . $post_type . '" data-taxonomy_type="'. $taxonomy_type .'">All</a></li>';
                foreach($terms as $term) {
                    $response .= '<li class="nav-item tax-nav-item"><a class="nav-link tax-nav-link" href="#!" data-slug="' . $term->slug . '" data-post_type="' . $post_type . '"  data-taxonomy_type="'. $taxonomy_type .'">'. $term->name .'</a></li>';
                }
                $response .= '</ul></div>';            
            }

            $response .= '<div style="position:relative;">';
                $response .= '<div class="list-loader"><div class="spinner-border" role="status"><span class="visually-hidden"></span></div></div>';
                $response .= '<div class="row portfolio-grid">';        
                if($posts->have_posts()) {
                    while ($posts->have_posts()) {
                        $posts->the_post();
                        
                        $template = dirname(__FILE__) . '/partials/post-item-block.php';
                        $response .= Threesixty_Post_List::get_template_part($template);
                    }
                }
                $response .= '</div>';
            $response .= '</div>';
        $response .= '</div>';
        
        wp_reset_postdata();
        
        print_r($response);
    }
    
    public static function filter_posts() {
        $taxonomy_type = filter_input(INPUT_POST, 'taxonomy_type');
        $term = filter_input(INPUT_POST, 'taxonomy');
        $post_type = filter_input(INPUT_POST, 'post_type');
        
        $post_args = array(
            'posts_per_page'   => -1,
            'orderby'       => 'title',
            'post_type'     => $post_type
        );
        
        if($term != 'all') {
            $post_args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy_type,
                    'field' => 'slug',
                    'terms' => $term,
                    'operator' => 'IN'
                )
            );
        }
        
        $posts = new WP_Query($post_args);
        
        $response = '';
        
        if($posts->have_posts()) {            
            while ($posts->have_posts()) {
                $posts->the_post();
                $template = dirname(__FILE__) . '/partials/post-item-block.php';
                $response .= Threesixty_Post_List::get_template_part($template);
            }
        }
        wp_reset_postdata();
        print_r($response);       
        wp_die();
    }
    
    
    public static function get_template_part($template = '') {
        
        if (is_file($template)) {
            ob_start();
            include $template;
            return ob_get_clean();
        }
        return false;        
    }
    
    
    
    

}