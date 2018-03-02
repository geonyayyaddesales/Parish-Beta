<?php
clearstatcache();
/**
 * parish functions and definitions
 *
 * @package parish
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'parish_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function parish_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on parish, use a find and replace
	 * to change 'parish' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'parish', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in two locations, the header and footer.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'parish' ),
		'footer' => __( 'Footer Menu', 'parish' ),
	) );
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'parish_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // parish_setup
add_action( 'after_setup_theme', 'parish_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function parish_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'parish' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Home RSS', 'parish' ),
		'id'            => 'home-rss',
		'description'   => __( 'Appears in the bottom left section of the home page.', 'parish' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title rss-title">',
		'after_title'   => '</h4>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Home MeetMe', 'parish' ),
		'id'            => 'meetme-rss',
		'description'   => __( 'Appears in the bottom right section of the home page.', 'parish' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title rss-title">',
		'after_title'   => '</h4>',

	) );

	register_sidebar( array(
		'name'          => __( 'Home Calendar', 'parish' ),
		'id'            => 'home-calendar',
		'description'   => __( 'Appears in the bottom right section of the home page.', 'parish' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
	) );
}
add_action( 'widgets_init', 'parish_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function parish_scripts2() {
	
	wp_enqueue_style ('google-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,700,700i&amp;subset=latin-ext');
	wp_enqueue_script( 'parish-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
  	wp_enqueue_script( 'font-awesome', 'https://use.fontawesome.com/0960f40152.js', array(), '20160614', true );
	wp_enqueue_script( 'parish-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'ticker', get_template_directory_uri() . '/js/jquery.webticker.min.js', array(), '20130115', true );
	wp_enqueue_script( 'tiny-sort', 'https://cdnjs.cloudflare.com/ajax/libs/tinysort/2.3.6/tinysort.min.js', array(), '20160810', true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'parish_scripts2' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

//Initialize the update checker.


function headlines_length_code(){ 
?>

<script>
jQuery(document).ready(function(){
	jQuery('.gce-list-event a').each(function(){
		$string = jQuery(this).html();
		$length = $string.length; 
		if($length > 38){
			$newString = $string.substring(0, 38);
			$newString = $newString+'...';
			jQuery(this).html($newString);
		}
	});
	jQuery('.widget_rss li a').each(function(){
		$string = jQuery(this).html();
		$length = $string.length; 
		if($length > 76){
			$newString = $string.substring(0, 76);
			$newString = $newString+'...';
			jQuery(this).html($newString);
		}
	});
});

</script>

<?php }

add_action('wp_footer', 'headlines_length_code');

//Social Icons Connect Fields
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_connect',
		'title' => 'Connect',
		'fields' => array (
			array (
				'key' => 'field_56b90070da954',
				'label' => 'Facebook',
				'name' => 'facebook',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_56b91111c6150',
				'label' => 'Shield',
				'name' => 'shield',
				'type' => 'checkbox',
				'choices' => array (
					'Shield Color' => 'Shield Color',
					'Shield White' => 'Shield White',
					'Keys Color' => 'Keys Color',
				),
				'default_value' => '',
				'layout' => 'vertical',
			),
			array (
				'key' => 'field_56b90091da955',
				'label' => 'Twitter',
				'name' => 'twitter',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => get_option( 'page_on_front' ),
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
add_action( 'init', 'register_cpt_event' );

function register_cpt_event() {

	$labels = array( 
		'name' => _x( 'Events', 'event' ),
		'singular_name' => _x( 'Event', 'event' ),
		'add_new' => _x( 'Add New', 'event' ),
		'add_new_item' => _x( 'Add New Event', 'event' ),
		'edit_item' => _x( 'Edit Event', 'event' ),
		'new_item' => _x( 'New Event', 'event' ),
		'view_item' => _x( 'View Event', 'event' ),
		'search_items' => _x( 'Search Events', 'event' ),
		'not_found' => _x( 'No events found', 'event' ),
		'not_found_in_trash' => _x( 'No events found in Trash', 'event' ),
		'parent_item_colon' => _x( 'Parent Event:', 'event' ),
		'menu_name' => _x( 'Events', 'event' ),
	);

	$args = array( 
		'labels' => $labels,
		'hierarchical' => false,
		
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-calendar-alt',
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array( 'slug' => 'events' ),
		'capability_type' => 'post'
	);

	register_post_type( 'event', $args );
}
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_58965a0221114',
	'title' => 'Alert New',
	'fields' => array(
  array (
			'key' => 'field_5a4d1bc81e2e4',
			'label' => 'Alert',
			'name' => 'alert',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'row',
			'button_label' => '',
			'sub_fields' => array (
		array(
			'key' => 'field_5a4c22d7352c1',
			'label' => 'Alert Active',
			'name' => 'alert_active',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array(
			'key' => 'field_5a4c0bc98c33a',
			'label' => 'Alert Start Date',
			'name' => 'alert_start_date',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'm/d/Y',
			'first_day' => 1,
			'return_format' => 'Y-m-d',
		),
		array(
			'key' => 'field_5a4c0bc98c33b',
			'label' => 'Alert Start Hour',
			'name' => 'alert_start_hour',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5a4c0bc98c33c',
			'label' => 'Alert Start Minute',
			'name' => 'alert_start_minute',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5a4c0bc98c33d',
			'label' => 'Alert Start Time AM-PM',
			'name' => 'alert_start_time_am_pm',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'AM' => 'AM',
				'PM' => 'PM',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => '',
			'layout' => 'vertical',
			'return_format' => 'value',
		),
		array(
			'key' => 'field_5a4c0bc98c33e',
			'label' => 'Alert End Date',
			'name' => 'alert_end_date',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'm/d/Y',
			'first_day' => 1,
			'return_format' => 'Y-m-d',
		),
		array(
			'key' => 'field_5a4c0bc98c33f',
			'label' => 'Alert End Time Hour',
			'name' => 'alert_end_time_hour',
			'type' => 'text',
			'instructions' => 'Should be',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5a4c0bc98c340',
			'label' => 'Alert End Time Minute',
			'name' => 'alert_end_time_minute',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5a4c0bc98c341',
			'label' => 'Alert End Time AM-PM',
			'name' => 'alert_end_time_am_pm',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'AM' => 'AM',
				'PM' => 'PM',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'save_other_choice' => 0,
			'default_value' => '',
			'layout' => 'vertical',
			'return_format' => 'value',
		),
  array(
			'key' => 'field_578d20ed33145',
			'label' => 'Recurring Event',
			'name' => 'recurring_event',
			'type' => 'radio',
			'instructions' => 'Select if this is recurring event, either Daily, Weekly, Monthly or Yearly.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'vertical',
			'choices' => array(
				'Not Recurring' => 'Not Recurring',
				'Daily' => 'Daily',
				'Weekly' => 'Weekly',
				'Monthly' => 'Monthly',
				'Yearly' => 'Yearly',
			),
			'default_value' => 'Not Recurring',
			'other_choice' => 0,
			'save_other_choice' => 0,
			'allow_null' => 0,
			'return_format' => 'value',
		),
		array(
			'key' => 'field_5a4c0bc98c342',
			'label' => 'Alert Text',
			'name' => 'alert_text',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => 138,
		),
		array(
			'key' => 'field_5a4c0bc98c343',
			'label' => 'Alert URL',
			'name' => 'alert_url',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
			),
		),
	),
),
	'location' => array(
		array(
			array(
				'param' => 'page_template',
				'operator' => '==',
				'value' => 'homepage template.php',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

endif;

function connect_shortcode_func(  ) {
$this_page_id = get_option( 'page_on_front' );
if(get_field ('twitter',$this_page_id) || get_field ('facebook',$this_page_id)) {
echo' 
	<div class="footer-right">
              <p class="connect">Connect with us:  </p>';
           if(get_field ('twitter', $this_page_id)) {
	echo'<a class="tw" href="'.get_field ("twitter", $this_page_id).'" title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a>'; }
if(get_field ('facebook' , $this_page_id)) {
	echo'<a class="fb" href="'.get_field ("facebook", $this_page_id).'" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a>'; }
       echo'</div><!-- footer-right --> ';
 }}
add_shortcode( 'connect', 'connect_shortcode_func' );

function shields_shortcode_func(  ) {
$this_page_id = get_option('page_on_front');
$field = get_field_object('shield',$this_page_id);
$value = $field['value'];
$choices = $field['choices'];
$seals = count($value);
$i = 0;
if( $value ): ?>
	<?php foreach( $value as $v ): ?>	
<?php if( $choices[ $v ]=='Keys Color'){echo '<a class="papacy-emblem seal-'.$i.'" href="http://w2.vatican.va/content/vatican/en.html" target="_blank" title="The Vatican">The Vatican</a>';} ?>
<?php if( $choices[ $v ]=='Shield White'){echo '<a class="footer-seal-white seal-'.$i.'" href="http://dioceseofbrooklyn.org/" target="_blank" title="Diocese of Brooklyn">Diocese of Brooklyn</a>';} ?>
<?php if( $choices[ $v ]=='Shield Color'){echo '<a class="footer-seal-color seal-'.$i.'" href="http://dioceseofbrooklyn.org/" target="_blank" title="Diocese of Brooklyn">Diocese of Brooklyn</a>';} ?>
	<?php $i++; endforeach; if ($seals>1){?>
<script> jQuery(document).ready(function(){
  jQuery('.footer-inner').css('padding-left', '160px'); 
  }); </script> 
<?php } endif;

}
add_shortcode( 'shields', 'shields_shortcode_func' );
add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return 600;') );