<?php
/*
Plugin Name: TextBlox
Description: Organize TextBlocks and insert it in your Articles or Pages using Shortcodes
Author: Powie
Author URI: http://www.powie.de
Plugin URI: http://www.powie.de/php/wordpress-textblox/
Version: 0.9.0
*/

/*
	Some Rules: TextBox -> TB
*/

$tb_version = "0.9.0";
// add our default options if they're not already there:
if (get_option('tb_version')  != $tb_version) {
    update_option('tb_version', $tb_version);}

// now let's grab the options table data
$tb_version = get_option('tb_version');

//Init
define('TB_PLUGIN_URL', plugins_url('',plugin_basename(__FILE__)).'/'); //PLUGIN DIRECTORY

add_action( 'init', 'create_tb_post_types' );
function create_tb_post_types() {
	 $labels = array(
		'name' => _x( 'TextBlox Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'TextBlox Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search TextBlox Categories' ),
		'all_items' => __( 'All TextBlox Categories' ),
		'parent_item' => __( 'Parent TextBlox Category' ),
		'parent_item_colon' => __( 'Parent TextBlox Category:' ),
		'edit_item' => __( 'Edit TextBlox Category' ),
		'update_item' => __( 'Update TextBlox Category' ),
		'add_new_item' => __( 'Add New TextBlox Category' ),
		'new_item_name' => __( 'New TextBlox Category Name' ),
  );
  	register_taxonomy('textblox_category',array('textblox'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'textblox-category' ),
  ));
	register_post_type( 'textblox',
		array(
			'labels' => array(
				'name' => __( 'TextBlox' ),
				'singular_name' => __( 'TextBlox' ),
				'edit_item'	=>	__( 'Edit TextBlox'),
				'add_new_item'	=>	__( 'Add TextBlox')
			),
			'public' => false,
			'menu_position' => 20,
			'menu_icon' => TB_PLUGIN_URL.'textblox_16.png',
			'show_ui' => true,
			'capability_type' => 'post',
			'rewrite' => array( 'slug' => 'textblox', 'with_front' => false ),
			'taxonomies' => array( 'TextBlox '),
			'supports' => array('title','editor','revisions')
		)
	);
}


add_action('restrict_manage_posts','restrict_tb_listings_by_categories');
function restrict_tb_listings_by_categories() {
    global $typenow;
    global $wp_query;
    if ($typenow=='textblox') {

		$tax_slug = 'textblox_category';

		// retrieve the taxonomy object
		$tax_obj = get_taxonomy($tax_slug);
		$tax_name = $tax_obj->labels->name;
		// retrieve array of term objects per taxonomy
		$terms = get_terms($tax_slug);

		// output html for taxonomy dropdown filter
		echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
		echo "<option value=''>Show All $tax_name</option>";
		foreach ($terms as $term) {
			// output each select option line, check against the last $_GET to show the current option selected
			echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
		}
		echo "</select>";
    }
}

add_shortcode('textblox', 'tb_shortcode');
// define the shortcode function
function tb_shortcode($atts) {
	extract(shortcode_atts(array(
		'cat'	=> '',
		'id'	=> ''
	), $atts));

	// stuff that loads when the shortcode is called goes here

		if (!empty($id)) {
			$tbs = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'p'	 			=> $id,
			'post_type'      => 'textblox',
			'post_status'    => null,
			'numberposts'    => -1) );
		} else {
			$tbs = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'textblox_category'	 => $cat,
			'post_type'      => 'textblox',
			'post_status'    => null,
			'numberposts'    => -1) );
		}

		global $wpdb; $catname = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE slug = '$cat'");

		if (!empty($cat)) {$qa_shortcode .= '<p class="faq-catname">'.$catname.'</p>';}

		if ($tbs) {
		foreach ($tbs as $tb) {

		$postslug = $tb->post_name;
		$title = $tb->post_title;
		$text = wpautop($tb->post_content);

		$tb_shortcode .= '<!-- '.$title.' -->'.$text;

		}}  // end slideshow loop

	$tb_shortcode = do_shortcode( $tb_shortcode );
	return (__($tb_shortcode));
}//ends the tb_shortcode function

add_filter('manage_edit-tb_faqs_columns', 'tb_columns');
function tb_columns($columns) {
    $columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Question' ),
		'textblox_category' => __( 'Categories' ),
		'date' => __( 'Date' )
	);
    return $columns;
}

add_action('manage_posts_custom_column',  'tb_show_columns');
function tb_show_columns($name) {
    global $post;
    switch ($name) {
        case 'textblox_category':
            $tb_cats = get_the_terms(0, "textblox_category");
			$cats_html = array();
			if(is_array($faq_cats)){
				foreach ($faq_cats as $term)
						array_push($cats_html, '<a href="edit.php?post_type=textblox&textblox_category='.$term->slug.'">' . $term->name . '</a>');

				echo implode($cats_html, ", ");
			}
			break;
		default :
			break;
	}
}

// create the admin menu
// hook in the action for the admin options page
add_action('admin_menu', 'add_tb_option_page');

function add_tb_option_page() {
	// hook in the options page function
	add_options_page('TextBlox', 'TextBlox', 6, __FILE__, 'tb_options_page');
}

function tb_options_page() { 	// Output the options page
	global $tb_version ?>
	<div class="wrap" style="width:500px">
	<?php screen_icon(); ?>
		<h2>TextBlox Plugin Reference</h2>
		<p>Use shortcode <code>[textblox id=123]</code> to insert your Blocks into a page.</p>

		<p>You're using TextBlox Version <?php echo $tb_version;?> by <a href="http://powie.de">Powie</a>.
	</div><!--//wrap div-->
<?php } ?>