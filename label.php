<?php
/*
Plugin Name: Label guide
Description:  Add skill level, estimated reading/completion time and author details to each blogpost with just a few clicks.
Author: Remco van Essen
Version: 1.0
Author URI: https://remcovanessen.co.uk

Forked from: http://halgatewood.com/easy-skill-facts-label
*/

/* ADDS */
add_shortcode( 'skill-label', 'ski_label_shortcode');
add_action( 'wp_head', 'ski_style');
add_action( 'init', 'ski_init');
add_filter( 'manage_edit-skill-label_columns', 'ski_modify_skillal_label_table' );
add_filter( 'manage_posts_custom_column', 'ski_modify_skillal_label_table_row', 10, 2 );

add_action( 'add_meta_boxes', 'ski_create_metaboxes' );
add_action( 'save_post', 'ski_save_meta', 1, 2 );


/* BASE FIELDS */
$skiional_fields = array(
					'skilllevel' 		=> __('Skill Level'),
					'estimatedtime' 	=> __('Estimated Time'),
);



/*
 * Init
 */
function ski_init()
{
	load_plugin_textdomain('wp-skill-label', false, 'wp-skill-label/languages/');

	$labels = array(
		'name' => __('Skill labels'),
		'singular_name' => __('Label'),
		'add_new' => __('Add New'),
		'add_new_item' => __('Add New Label'),
		'edit_item' => __('Edit Label'),
		'new_item' => __('New Label'),
		'all_items' => __('All Labels'),
		'view_item' => __('View Label'),
		'search_items' => __('Search Labels'),
		'not_found' =>  __('No labels found'),
		'not_found_in_trash' => __('No labels found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => __('Labels')

	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => false,
		'capability_type' => 'post',
		'has_archive' => false, 
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon' => plugins_url('assets/label_1.png', __FILE__),
		'supports' => array( 'title' )
	); 
	register_post_type('skill-label', $args);

}

/*
 * Meta Box with Data
 */
function ski_create_metaboxes()
{
	add_meta_box( 'ski_create_metabox_1', 'Skill Label Options', 'ski_create_metabox_1', 'skill-label', 'normal', 'default' );

}

function ski_create_metabox_1()
{
	global $post, $skiional_fields;	
	$meta_values = get_post_meta( $post->ID );
	
	$pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );
	$posts = get_posts( array( 'numberposts' => -1 ) );
	
	
	?>
	<?php echo "<p>Author details will be added automatically 😃</p>" ?>
	
	<?php
	foreach( $skiional_fields as $name => $skiional_field ) { ?>

	<div style="padding: 3px 0;">
		<div style="width: 75px; margin-right: 10px; float: left; text-align: right; padding-top: 5px;">
			<?php echo $skiional_field ?>

		</div>

		<input type="text" style=" float: left; width: 120px;" name="<?php echo $name ?>" value="<?php if(isset($meta_values['_' . $name])) { echo esc_attr( $meta_values['_' . $name][0] ); } ?>" />
	
		<div style="clear:both;"></div>

	</div>

<?php
	}
}

function ski_save_meta( $post_id, $post ) 
{
	global $skiional_fields;
	foreach( $skiional_fields as $name => $skiional_field ) 
	{
		if ( isset( $_POST[ $name ] ) ) { update_post_meta( $post_id, '_' . $name, strip_tags( $_POST[ $name ] ) ); }
	}
	
	if ( isset( $_POST[ 'pageid' ] ) ) { update_post_meta( $post_id, '_pageid', strip_tags( $_POST[ 'pageid' ] ) ); }
}


/*
 * Add Column to WordPress Admin 
 * Displays the shortcode needed to show label
 *
 * 2 Functions
 */
 
function ski_modify_skillal_label_table( $column ) 
{ 

	$columns = array(
		'cb'       			=> '<input type="checkbox" />',
		'title'    			=> 'Title',
		'ski_shortcode'    => 'Shortcode',
		'date'     			=> 'Date'
	);

	return $columns;
}
function ski_modify_skillal_label_table_row( $column_name, $post_id ) 
{
 	if($column_name == "ski_shortcode")
 	{
 		echo "[skill-label id={$post_id}]";
 	}
 	
}


/*
 * output our style sheet at the head of the file
 * because it's brief, we just embed it rather than force an extra http fetch
 *
 * @return void
 */
function ski_style() 
{
?>

<style type='text/css'>
	.wp-skill-label { border: 1px solid #ccc; font-family: helvetica, arial, sans-serif; font-size: .9em; width: 22em; padding: 1em 1.25em 1em 1.25em; line-height: 1.4em; margin: 1em; }
	.wp-skill-label hr { border:none; border-bottom: solid 8px #666; margin: 3px 0px; }
	.wp-skill-label .heading { font-size: 2.6em; font-weight: 900; margin: 0; line-height: 1em; }
	.wp-skill-label .indent { margin-left: 1em; }
	.wp-skill-label .small { font-size: .8em; line-height: 1.2em; }
	.wp-skill-label .item_row { border-top: solid 1px #ccc; padding: 3px 0; }
	.wp-skill-label .amount-per { padding: 0 0 8px 0; }
	.wp-skill-label .daily-value { padding: 0 0 8px 0; font-weight: bold; text-align: right; border-top: solid 4px #666; }
	.wp-skill-label .f-left { float: left; }
	.wp-skill-label .f-right { float: right; }
	.wp-skill-label .noborder { border: none; }
	
	.cf:before,.cf:after { content: " "; display: table;}
	.cf:after { clear: both; }
	.cf { *zoom: 1; } 
ul.levelhead {
    list-style: none;
    text-align: center;
    float: left;
    padding-left: 15px;
    padding-right: 15px;
    color: #191919;
}
li.avatarimage img {
    border-radius: 50%;
    height: 60px;
    width: auto;
    float: right;
    color:#191919;
}
ul#levelone {
    border-right: 2px solid #191919;
}
ul#levelthree li:first-child {
    width: 50%;
    float: left;
}
ul#leveltwo {
    border-right: 2px solid #191919;
}
div.slider {
    width: 520px;
    display: block;
    margin: auto;
}
ul#levelthree {
    min-width: 185px;
}
li {
    text-transform: lowercase;
}
li:nth-child(2) {
    font-style: italic;
}
li:nth-child(1) {
    font-weight: 700;
}

@media (max-width:650px){
	.slider{
		zoom:0.6;
	}
}
</style>


<?php
}


/*
 *
 * @param array $atts
 * @return string
 */
function ski_label_shortcode($atts) 
{
	$id = (int) isset($atts['id']) ? $atts['id'] : false;
	$width = (int) isset($atts['width']) ? $atts['width'] : 22;	
	
	if($id) { return ski_label_generate($id, $width); }
	{
		global $post;
	
		$label = get_posts( array( 'post_type' => 'skill-label', 'meta_key' => '_pageid', 'meta_value' => $post->ID ));
		
		if($label)
		{
			$label = reset($label);
			return ski_label_generate( $label->ID, $width );

		}
	}
}


/*
 * @param integer $contains
 * @param integer $reference
 * @return integer
 */
function ski_percentage($contains, $reference) 
{
	return round( $contains / $reference * 100 );
}


/*
 * @param array $args
 * @return string
 */
function ski_label_generate( $id, $width = 22 ) 
{
	global $skiional_fields;
	
	$label = get_post_meta( $id );
	
	if(!$label) { return false; }
	
	// GET VARIABLES
	foreach( $skiional_fields as $name => $skiional_field )
	{
		$$name = $label['_' . $name][0];	
	}
	$rtn = $_POST['rtn'] ?? '';

	
	$rtn .= " <div class='slider'>";
	
	$rtn .= "		<ul class='levelhead' id='levelone'>
						<li>Skill Level</li>
						<li>" . ($skilllevel) . "</li>
						</ul>\n";
			
	$rtn .= "		<ul class='levelhead' id='leveltwo'>
					<li>Estimated Time</li>
					<li>" . $estimatedtime . "</li>
					</ul>\n";
	
	$rtn .= "		<ul class='levelhead' id='levelthree'>
					<li>By ". get_the_author() ."</li>
					<li class='avatarimage'>".  get_avatar( get_the_author_meta( 'ID' ), 32 ) ."</li>
					</ul>\n";
	
  
	$rtn .= "</div> <!-- /wp-skill-label -->\n\n";
	return $rtn;  
}

?>