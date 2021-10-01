<?php

class Threesixty_Post_List_Admin {
    
    private static $initiated = false;
    private static $date_format = 'Y/m/d H:i:s';

    public static function init() {
        
        if ( ! self::$initiated ) {
            self::init_hooks();
	}        
    }
    
    public static function init_hooks() {
       
        self::$initiated = true;
        
        add_action( 'admin_init', array( 'Threesixty_Post_List_Admin', 'admin_init' ) );
        add_action( 'admin_menu', array( 'Threesixty_Post_List_Admin', 'admin_menu' ));
        add_action( 'admin_enqueue_scripts', array( 'Threesixty_Post_List_Admin', 'load_resources' ));
        add_action( 'admin_post_save_threesixty_post_list', array( 'Threesixty_Post_List_Admin', 'save_threesixty_post_list' ));
        //add_action( 'delete_threesixty_post_list', array( 'Threesixty_Post_List_Admin', 'delete_threesixty_post_list' ) );        
    }
    
    public static function admin_init() {
        if ( get_option( 'Activated_Threesixty_Post_List' ) ) {
            delete_option( 'Activated_Threesixty_Post_List' );            
	}
        
        load_plugin_textdomain( 'threesixty-post-list' );
    }
    
    public static function admin_menu() {
        add_menu_page(
            __( 'Threesixty Post List', 'threesixty-post-list' ),
            'Threesixty Post List',
            'manage_options',
            'threesixty-post-list',
            array('Threesixty_Post_List_Admin', 'display_setting_page'),
            6);
        
        add_submenu_page(
            'threesixty-post-list', 
            'Edit Post List',
            '', 
            'manage_options',
            'threesixty-post-list-edit',
            array('Threesixty_Post_List_Admin', 'display_edit_threesixty_post_list'));
        
        add_submenu_page(
            'threesixty-post-list', 
            'Edit Post List',
            '', 
            'manage_options',
            'threesixty-post-list-delete',
            array('Threesixty_Post_List_Admin', 'delete_threesixty_post_list'));
    }

    public static function load_resources() {
        $bootstrapCSSFile = 'assets/node_modules/bootstrap/dist/css/bootstrap.min.css';
        $adminCSSFile = 'assets/css/admin-style.css';
        
        wp_register_style( 'bootstrap', plugin_dir_url(__FILE__) . $bootstrapCSSFile , array(), filemtime(plugin_dir_path(__FILE__) . $bootstrapCSSFile) );
	wp_enqueue_style( 'bootstrap');
        wp_register_style( 'post-list-admin-style', plugin_dir_url(__FILE__) . $adminCSSFile , array(), filemtime(plugin_dir_path(__FILE__) . $adminCSSFile) );
	wp_enqueue_style( 'post-list-admin-style');
    }
    
    public static function display_setting_page() {
        self::threesixty_post_list_form();
        self::show_threesixty_post_list();
    }
    
    public static function threesixty_post_list_form($form_data = null) {  
        
        $form_errors = get_transient( 'threesixty_post_list_create_form_erros' );
        $form_success = get_transient( 'threesixty_post_list_create_form_success' );
        $delete_success = get_transient('threesixty_post_list_delete_msg');
        
        if(!empty($form_errors)) {
    ?>
            <div class="error">
                <p>Error occur! data does not saved in database. please resolve below errors.</p>
                <ul>
                <?php
                    foreach ($form_errors as $error) {
                        echo '<li class="error-text"> - '. $error .'</li>';
                    }
                ?>
                </ul>
            </div>
    <?php
            delete_transient('threesixty_post_list_create_form_erros');
        }
        
        if(!empty($form_success)) {
            echo '<div class="notice notice-success">' . $form_success . '</div>';
            delete_transient('threesixty_post_list_create_form_success');
        }
        
        if(!empty($delete_success)) {
            echo '<div class="notice notice-success">' . $delete_success . '</div>';
            delete_transient('threesixty_post_list_delete_msg');
        }
        
        $args = array( 'public' => true);
        $output = 'objects';
        $operator = 'and';
        
        $post_types = get_post_types($args, $output, $operator);
        
        $taxonomy_types = get_taxonomies();        
    ?>
    <div class="plugin-content">
            <div class="container">
                <h1>Threesixty Post List</h1>
                <div class="main-wapper threesixty-form">
                    <form autocomplete="off" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                        <input type="hidden" name="action" value="save_threesixty_post_list">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Title</label>
                            <input class="col-sm-8" name="title" type="text" value="<?php echo ($form_data != null && $form_data['title']) ? $form_data['title'] : ''; ?>">
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Post Type</label>
                            <select class="form-select" name="post_type" value="<?php echo ($form_data != null && $form_data['post_type']) ? $form_data['post_type'] : ''; ?>">
                                <?php
                                foreach ($post_types as $row) :                                    
                                echo ($form_data != null && $form_data['post_type'] == $row->name) ? '<option value="' . $row->name . '" selected>' . $row->label . '</option>' : '<option value="' . $row->name . '">' . $row->label . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Taxonomy Type</label>
                            <select class="form-select" name="taxonomy_type" value="<?php echo ($form_data != null && $form_data['taxonomy_type']) ? $form_data['taxonomy_type'] : ''; ?>">
                                <?php
                                echo '<option value=""> - select - </option>';
                                foreach ($taxonomy_types as $row) :                                    
                                echo ($form_data != null && $form_data['taxonomy_type'] == $row) ? '<option value="' . $row . '" selected>' . $row . '</option>' : '<option value="' . $row . '">' . $row . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Item per page</label>                            
                            <input class="col-sm-8" name="item_per_page" type="number" min="-1" max="50" value="<?php echo ($form_data != null && $form_data['item_per_page']) ? $form_data['item_per_page'] : ''; ?>">
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Is Active</label>
                            <input class="col-sm-8 form-check-input" style="margin-top: 10px;" name="is_active" type="checkbox" <?php echo ($form_data != null && $form_data['is_active'] == 1 ? 'checked' : '') ; ?>>
                        </div>                        
                        
                        <input type="hidden" name="date_created" value="<?php echo ($form_data != null && $form_data['date_created'] != '') ? $form_data['date_created'] : date(self::$date_format); ?>">
                        
                        <input type="hidden" name="date_updated" value="<?php echo ($form_data != null && $form_data['date_updated'] != '') ? $form_data['date_updated'] : date(self::$date_format); ?>">
                        
                        <input type="hidden" name="id" value="<?php echo ($form_data != null && $form_data['id'] != '') ? $form_data['id'] : ''; ?>">
                        
                        <div class="row mb-3 d-grid gap-2 col-md-2" style="margin-left: 170px;">
                            <button class="btn btn-secondary btn-lg" type="submit">Save</button>
                        </div>
                    </form>
                    
                </div>
                <p>&nbsp;</p>
            </div>    
        </div>    
    <?php    
    }    
    
    public static function save_threesixty_post_list() {
        
        $form_errors = array();
        $id;
        $title;
        $date_created;
        $date_updated;
        $post_type;
        $taxonomy_type = '';
        $item_per_page;
        $is_active = (filter_input(INPUT_POST, 'is_active') !== null && filter_input(INPUT_POST, 'is_active') !== '') ? true : false;
        
        if(filter_input(INPUT_POST, 'id') !== null && filter_input(INPUT_POST, 'id') !== '') {
            $id = sanitize_text_field(filter_input(INPUT_POST, 'id'));
        }
        
        if(filter_input(INPUT_POST, 'title') == null && filter_input(INPUT_POST, 'title') == '') {
            array_push($form_errors, 'Title text is required');
        } else {
            $title = sanitize_text_field(filter_input(INPUT_POST, 'title'));
        }
        
        if(filter_input(INPUT_POST, 'post_type') == null && filter_input(INPUT_POST, 'post_type') == '') {
            array_push($form_errors, 'Post Type text is required');
        } else {
            $post_type = sanitize_text_field(filter_input(INPUT_POST, 'post_type'));
        }
        
        if(filter_input(INPUT_POST, 'taxonomy_type') !== null && filter_input(INPUT_POST, 'taxonomy_type') !== '') {
            $taxonomy_type = sanitize_text_field(filter_input(INPUT_POST, 'taxonomy_type'));
        } else {
            $taxonomy_type = null;
        }
        
        if(filter_input(INPUT_POST, 'date_created') !== null && filter_input(INPUT_POST, 'date_created') !== '') {
            $date_created = date(self::$date_format, strtotime(sanitize_text_field(filter_input(INPUT_POST, 'date_created'))));
        } else {
            $date_created = date(self::$date_format);
        }
        
        if(filter_input(INPUT_POST, 'date_updated') !== null && filter_input(INPUT_POST, 'date_updated') !== '') {
            $date_updated = date(self::$date_format, strtotime(sanitize_text_field(filter_input(INPUT_POST, 'date_updated'))));
        } else {
            $date_updated = date(self::$date_format);
        }
                
        if(filter_input(INPUT_POST, 'item_per_page') !== null && filter_input(INPUT_POST, 'item_per_page') !== '') {
            $item_per_page = sanitize_text_field(filter_input(INPUT_POST, 'item_per_page'));
        } else {
            $item_per_page = -1;
        }
          
        
        if(!empty($form_errors)) {            
            if(isset($id)) {
                set_transient('threesixty_post_list_create_form_erros', $form_errors);
                wp_safe_redirect(esc_url(site_url( '/wp-admin/admin.php?page=threesixty-post-list-edit&postlistid=' . $id )));            
            } else {
                set_transient('threesixty_post_list_create_form_erros', $form_errors);
                wp_safe_redirect(esc_url(site_url( '/wp-admin/admin.php?page=threesixty-post-list' )));            
            }            
            
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . "threesixty_post_list";            
            if(isset($id)){
                $wpdb->update(
                $table_name,                        
                    array(
                        'title' => $title,
                        'date_created' => $date_created,
                        'date_updated' => $date_updated,
                        'post_type' => $post_type,
                        'taxonomy_type' => $taxonomy_type,
                        'item_per_page' => $item_per_page,
                        'is_active' => $is_active
                    ),
                    array(
                        'id' => $id
                    ),
                    array('%s', '%s', '%s', '%s', '%s', '%d', '%d'),
                    array( '%d' )
                    );
                
                set_transient('threesixty_post_list_create_form_success', 'Data saved successfully');
            } else {
                $wpdb->insert(
                $table_name,
                    array(
                        'title' => $title,
                        'date_created' => $date_created,
                        'date_updated' => $date_updated,
                        'post_type' => $post_type,
                        'taxonomy_type' => $taxonomy_type,
                        'item_per_page' => $item_per_page,
                        'is_active' => $is_active
                    ),
                    array('%s', '%s', '%s', '%s', '%s', '%d', '%d')
                    );
                
                set_transient('threesixty_post_list_create_form_success', 'Data saved successfully');
            }
            
            wp_safe_redirect(esc_url(site_url( '/wp-admin/admin.php?page=threesixty-post-list' )));
        }
                
        exit();
    }
    
    public static function show_threesixty_post_list(){
        global $wpdb;
        $table_name = $wpdb->prefix . "threesixty_post_list";
        
        $result = $wpdb->get_results("SELECT id, title, date_created, date_updated, post_type, taxonomy_type, item_per_page, is_active FROM $table_name");
        
        if(!empty($result)){
        ?>
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Post Type</th>
                        <th>Item Per Page</th>
                        <th>Is Active</th>
                        <th>Short Code</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($result as $row) {
                ?>
                    <tr>
                        <td><?php echo "<p>$row->title</p>" ?></td>
                        <td><?php echo "<p>$row->post_type</p>" ?></td>
                        <td><?php echo "<p>$row->item_per_page</p>" ?></td>
                        <td><?php echo "<p>$row->is_active</p>" ?></td>
                        <td><?php echo '<p>[threesixty_post_list id="' . $row->id . '"]</p>' ?></td>
                        <td><a href="<?php echo add_query_arg( array( 'page' => 'threesixty-post-list-edit', 'postlistid' => $row->id ), admin_url('admin.php') ); ?>">edit</a></td>
                        <td><a href="<?php echo add_query_arg( array( 'page' => 'threesixty-post-list-delete', 'postlistid' => $row->id ), admin_url('admin.php') ); ?>">delete</a></td>
                    </tr>
                <?php
                }
                ?>                    
                </tbody>
            </table>          
        <?php
        }
        else {
            print_r('No post list added yet, please add using above form.');
        }
    }
    
    public static function display_edit_threesixty_post_list() {
        
        if(isset($_GET['postlistid'])) {
            
            $postlistid = $_GET['postlistid'];
            
            global $wpdb;
            $table_name = $wpdb->prefix . "threesixty_post_list";
            $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $postlistid));
            
            $formatedDateCreated = date('Y/m/d H:i:s', strtotime($result->date_created));
            
            $formatedDateUpdated = date('Y/m/d H:i:s');
            
            $form_data = array (
              'id' => $result->id,
              'title' => $result->title,
              'post_type' => $result->post_type,
              'taxonomy_type' => $result->taxonomy_type,  
              'item_per_page' => $result->item_per_page,
              'is_active' => $result->is_active,
              'date_created' => $formatedDateCreated,
              'date_updated' => $formatedDateUpdated
            );

            self::threesixty_post_list_form($form_data);
            
        } else {
            wp_safe_redirect(esc_url(site_url( '/wp-admin/admin.php?page=threesixty-post-list' )));
        }
    }
    
    public static function delete_threesixty_post_list(){
        
        $id = filter_input(INPUT_GET, 'postlistid');
        global $wpdb;
        $table_name = $wpdb->prefix . "threesixty_post_list";
        $wpdb->delete($table_name, array( 'id' => $id ), array( '%d' ));
        
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        
        if($result == null) {
            set_transient('threesixty_post_list_delete_msg', 'Data deleted successfully');
        } else {
            set_transient('threesixty_post_list_delete_msg', 'Data not deleted, please try again');
        }
        $wpdb->close();
        wp_safe_redirect(esc_url(site_url( 'http://localhost.the360virtualtour.com/wp-admin/admin.php?page=threesixty-post-list' )));
        
        exit();
    }
    
    
}
 