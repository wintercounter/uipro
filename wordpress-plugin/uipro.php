<?php

/**
 * Plugin Name: Ui-Pro
 * Plugin URI: http://codecanyon.com/user/wintercounter
 * Description: The WordPress version of the UI-PRO plugin.
 * Author: wintercounter
 * Version: 1.0
 * Author URI: http://wintercounter.me
 */
 
define('UIPRO_VERSION', '1.0');
define('UIPRO_PLUGIN_URL', plugin_dir_url( __FILE__ ));

global $uipro_options, $UIPRO_msg;
$uipro_options = array();
$UIPRO_msg = false;
 
/* for plugin Settings in Admin */
function UIPRO_admin() {
    include('settings.php');
}
 
add_action('init', 'UIPRO_init');
add_action('init', 'UIPRO_post_type');
add_action('wp_footer', 'UIPRO_add_content');
add_action('admin_menu', 'UIPRO_admin_actions');
add_action('update_option_UIPRO_option', 'UIPRO_update_options');
add_action('save_post', 'UIPRO_save_item');
add_action('admin_init', 'UIPRO_add_metabox');
 
function UIPRO_admin_actions() {
    add_options_page('Ui-Pro Settings', 'Ui-Pro Settings', 'manage_options', 'uipro_settings', 'UIPRO_admin');
}
 
function UIPRO_init() {
    
    UIPRO_get_options();
    
    if (!is_admin()) {
        
        wp_register_script('ui-pro', UIPRO_PLUGIN_URL . 'assets/ui-pro.js', array('jquery'));
        wp_enqueue_script('ui-pro');
        
        wp_register_style('ui-pro', UIPRO_PLUGIN_URL . 'assets/style.css', array(), '1.0', 'all');
        wp_enqueue_style('ui-pro');
        
    }
    else{
				wp_register_style('ui-pro', UIPRO_PLUGIN_URL . 'assets/admin.css', array(), '1.0', 'all');
        wp_enqueue_style('ui-pro');
    }
    
}
 
function UIPRO_install() {

    add_option('UIPRO_option_left', 'off');
    add_option('UIPRO_option_right', 'off');
    add_option('UIPRO_option_threshold', '40');
 
}
 
function UIPRO_add_content() {
    
    global $uipro_options;
    
    $args = array(
    'numberposts'     => -1,
    'orderby'         => 'title',
    'order'           => 'ASC',
    'post_type'       => 'UIPRO_links',
    'post_status'     => 'publish' );
    
    $posts = get_posts($args);
    
    $links = array(
        'left' => array(),
        'right' => array()
    );
    
    $items_left = '';
    $items_right = '';
    $additional_styles = '<style>';
    
    $i = 1;
    
    foreach($posts as $post){
        
        $urlposition = get_post_meta( $post->ID, 'UIPRO_position', true );
				$urllink = get_post_meta( $post->ID, 'UIPRO_link', true );
        $urlimg = get_post_meta( $post->ID, 'UIPRO_img', true );
				$target = get_post_meta( $post->ID, 'UIPRO_target', true );
        
        $var_name = 'items_'.$urlposition;
        
                  
        if(stripos($urlimg,"/")){
            $class = 'uipro_randclass'.rand(0,10000);
            $additional_styles .= '.' . $class . '{background-image: url(' . $urlimg . ')}';
        }
        else{
            $class = $urlimg;
        }
        
        if($$var_name !== ''){
            $$var_name .= ',';
        }
        
        $$var_name .= '
        "item' . $i . '" : {
            "label" : "' . $post->post_title . '",
            "link" : "' . $urllink . '",
            "klass" : "' . $class . '",
            "target" : "' . $target . '"
        }';
        
        $i++;
        
    }
    
    $items_left = ($items_left === '' || $uipro_options['left'] == 'off') ? 'false' : "{".$items_left."}";
    $items_right = ($items_right === '' || $uipro_options['right'] == 'off') ? 'false' : "{".$items_right."}";
    
    $out = 
    '<script type="text/javascript">
    
        ( function($) {
            $(document).ready(function() {
            
                $.uiPro({
                    leftMenu : ' . $items_left . ',
                    rightMenu : ' . $items_right . ',
                    threshold : ' . $uipro_options['threshold'] . '
                });
            
            });
        } ) ( jQuery );
    
    </script>
    ' . $additional_styles . '</style>';
    
    echo $out;
    
}

/**
 * Display the metabox
 */

function UIPRO_add_metabox() {
	add_meta_box( 'custom-metabox', __( 'Ui-Pro Options' ), 'UIPRO_display_metabox', 'uipro_links', 'normal', 'high' );
}

function UIPRO_display_metabox() {
    
				global $post;
        $urlposition = get_post_meta( $post->ID, 'UIPRO_position', true );
				$urllink = get_post_meta( $post->ID, 'UIPRO_link', true );
        $urldesc = get_post_meta( $post->ID, 'UIPRO_img', true );
				$target = get_post_meta( $post->ID, 'UIPRO_target', true );
	
	?>
	
	<p><label for="UIPRO_link">Target Url:<br />
		<input size="50" name="UIPRO_link" value="<?php if( $urllink ) { echo $urllink; } ?>" /></label></p>
	<p><label for="UIPRO_img">Image:<br />
		<input size="50" type="text" name="UIPRO_img" value="<?php if( $urldesc ) { echo $urldesc; } ?>"></label><br>
        <span>Leave blank if no need for image. Else you can use the provided images or an URL to your own image.</span></p>
        <p><label for="UIPRO_position">Assign to which menu:<br />
		<select name="UIPRO_position">
                    <option value="left"<?php if($urlposition == 'left') { echo ' SELECTED';} ?>>Left</option>
                    <option value="right"<?php if($urlposition == 'right') { echo ' SELECTED';} ?>>Right</option>
                </select>
        </label></p>
        <p><label for="UIPRO_position">Target Attribute:<br />
		<select name="UIPRO_target">
                    <option value="_self"<?php if($target == '_self') { echo ' SELECTED';} ?>>_self</option>
                    <option value="_blank"<?php if($target == '_blank') { echo ' SELECTED';} ?>>_blank</option>
                    <option value="_parent"<?php if($target == '_parent') { echo ' SELECTED';} ?>>_parent</option>
                    <option value="_top"<?php if($target == '_top') { echo ' SELECTED';} ?>>_top</option>
                </select>
        </label></p>
<?php
}

function UIPRO_save_item($post_id) {
    
    if(isset($_POST['UIPRO_link'])){
        update_post_meta($post_id, 'UIPRO_link', $_POST['UIPRO_link']); 
    }
    if(isset($_POST['UIPRO_img'])){
        update_post_meta($post_id, 'UIPRO_img', $_POST['UIPRO_img']); 
    }
    if(isset($_POST['UIPRO_position'])){
        update_post_meta($post_id, 'UIPRO_position', $_POST['UIPRO_position']); 
    }
    if(isset($_POST['UIPRO_target'])){
        update_post_meta($post_id, 'UIPRO_target', $_POST['UIPRO_target']); 
    }
    
}

function UIPRO_update_options() {
    
    global $UIPRO_msg;
    
    update_option('UIPRO_option_left', $_POST['left']);
    update_option('UIPRO_option_right', $_POST['right']);
    update_option('UIPRO_option_threshold', (is_numeric($_POST['threshold'])) ? $_POST['threshold'] : 40);
    
    $UIPRO_msg = "Settings successfully updated!";
    
}

function UIPRO_get_links($category){
    
    $ret = array();
    
    if(!function_exists('wp_list_bookmarks')){
        
        return get_links($category, 'before', 'after', 'between', true);
        
    }
    
    return wp_list_bookmarks( array(
    
        'limit'            => -1,
        'category'         => $category,
        'echo'             => 0,
        'category_before'  => '<li id=%id class=%class>',
        'category_after'   => '</li>'
    
    ));
    
}

function UIPRO_post_type() {
    
  $labels = array(
    'name' => 'UI-Pro Links',
    'singular_name' => 'Links',
    'add_new' => 'Add New',
    'add_new_item' => 'Link',
    'edit_item' => 'Edit Link',
    'new_item' => 'New Link',
    'all_items' => 'All Links',
    'view_item' => 'View Link',
    'search_items' => 'Search Links',
    'not_found' =>  'No linksbooks found',
    'not_found_in_trash' => 'No links found in Trash', 
    'parent_item_colon' => '',
    'menu_name' => 'UI-Pro Links'
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true, 
    'query_var' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'custom-fields' )
  ); 

  register_post_type( 'UIPRO_links', $args );
  
}

function UIPRO_uninstall() {
    delete_option('UIPRO_option_left');
    delete_option('UIPRO_option_right');
    delete_option('UIPRO_option_threshold');
}

function UIPRO_get_options(){
    
    global $uipro_options;
    
    $uipro_options['left'] = get_option('UIPRO_option_left');
    $uipro_options['right'] = get_option('UIPRO_option_right');
    $uipro_options['threshold'] = get_option('UIPRO_option_threshold');
    
}
 
register_activation_hook( __FILE__, 'UIPRO_install' );
register_uninstall_hook( __FILE__, 'UIPRO_uninstall' );