-To Add (Authors Page, 404 Page)

WordPress Notes:
-Wordpress will default with posts being on the home page (index.php). If you want to change that, login through WP and go to setting page and change front display to static page then select front page and select post page.
-Revisions might be turned off you should be able to switch them back on through the functions file.
-Next you need to crate a file name front-page.php to layout your home page. Details below

Handy Plugins
-301 Redirects - CloudFlare could handle this or use (Redirections)
-SEO (Yoast SEO)
-Forms (Contact Form 7 & Flamingo - flamingo_subject: "From Contact Form" ) or Gravty Forms
-SMTP (Easy WP SMTP Settings) Email: Gmail Email, From: Name, SMTP Host: smtp.gmail.com, Encryption: SSL, SMTP Port: 465, SMTP Authentication: Yes, SMP Username: Gmail Email, SMTP Password: Gmail Password
-Compress CSS & JS - CloudFlare could handle this or use this plugin (Autoptimize)
-Grid in Content Area (Page Builder)
-Regenerate Thumbainls - useful for development
-More Advanced Site Search (Relevanssi – A Better Search) / WP Extended Search adss additional content to searh for like authors and WP adding meta data
-ACF (Advanced Custom Fields) - Allows you to add custom fields to WP
-Post Types Order - Allows you to drag and drop sort items in WP useful with ACF
-Custom Permalinks - Allows you to change a pages path rather than just the slug name
-Backup/Move Site use - All-In-One migration plugin

Functions file
//This hook is called after the theme is initialized. Used to perform basic setup, registration, and init actions for a theme.
function wpc_theme_setup() {

  //Add Featured image to WP - so WP provides the ability to add images to posts and pages
  add_theme_support('post-thumbnails');
  
  //Add Post Formats to WP - so WP provides the abbility to select differnt post types
  //Here's the full list of options.  Remove what you don't need.
  add_theme_support('post-formats', array('aside', 'gallery', 'link', 'quote', 'status', 'video', 'audio', 'chat'));
  
}
add_action('after_setup_theme', 'wpc_theme_setup');

//Set Default Display Image size in editor
function custom_image_size() {
    //update_option('image_default_align', 'center' );
    update_option('image_default_size', 'full' );
}
add_action('after_setup_theme', 'custom_image_size');

/*
* Force the kitchen sink to always be on
*/
add_filter( 'tiny_mce_before_init', 'tcb_force_kitchensink_open' );
function tcb_force_kitchensink_open( $args ) {
  $args['wordpress_adv_hidden'] = false;
  return $args;
}

//Add Format button to second row.
add_filter( 'mce_buttons_2', 'fb_mce_editor_buttons' );
function fb_mce_editor_buttons( $buttons ) { 
	array_unshift( $buttons, 'styleselect' ); 
	return $buttons; 
}

//Add styles classes to the "Styles" drop-down
add_filter( 'tiny_mce_before_init', 'fb_mce_before_init' );

function fb_mce_before_init( $settings ) {

    $style_formats = array(
        array(
            'title' => 'Expand',
            'selector' => 'img',
            'classes' => 'expand'
            ),
    );
    $settings['style_formats'] = json_encode( $style_formats );
	unset($init['preview_styles']);
    return $settings;
}


//Adding styles and scripts
function wp_theme_styles-scripts(){
  
  //Load Styles - unique handle       -    style path                              -          list of dependencids  - version
  wp_enqueue_style( 'wpc-style', get_stylesheet_uri() );
  wp_enqueue_style( 'twentyseventeen-colors-dark', get_theme_file_uri( '/css/additional.css' ), array( 'wpc-style' ), '1.0' );
  
  //Load Scripts - unique handle       -    script path      -     list of dependencies  -  version - footer or header
  wp_enqueue_script('name', get_template_directory_uri().'/js/script.js', array( 'jquery' ), '1.0', true);

}
add_action('wp_enqueue_scripts', 'wp_theme_styles-scripts');


//Menu Container Locations
functions wpc-theme-menu(){
  register_nav_menu('newMenu', __('New Menu'))
}
add_action('init', 'wpc-theme-menu')


//Modify the length of characters an excerpt is displayed
function set_excerpt_length(){
  return 45;
}
add_filter('excerpt_length', 'set_excerpt_length');

//Change the look of the [...] at the end of a excerpt
function excerpt_readmore($more) {
	return '... <a href="'. get_permalink($post->ID) . '" class="readmore">' . 'Read More' . '</a>';
}
add_filter('excerpt_more', 'excerpt_readmore');

//Custom Excerpt
function emailExcerpt($num) {
    $limit = $num+1;
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    array_pop($excerpt);
	//$excerpt = implode(" ",$excerpt)."... (<a href='" .get_permalink($post->ID) ." '>Read more</a>)";
	$excerpt = implode(" ",$excerpt)."...";
    return $excerpt;
}

//Add Widgets Container to WP and configure some of the output
function wpc_init_widgets($id){
  
  //Widget Container
  register_sidebar(array(
    'name' => 'Sidebar',
    'id' => 'sidebar',
    'before_widget' => '<div class="sidebarC">',
    'after_widget' => '</div>',
    'before_title' => '<h4>',
    'after_title' => '</h4>',
  ));
  
  //Widget Container
  register_sidebar(array(
    'name' => 'WidgetContainer2',
    'id' => 'widgetc2',
    'before_widget' => '<div class="widget2">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  ));
}
add_action('widgets_init', 'wpc_init_widgets');

// Allow svg upload
add_filter('upload_mimes', 'add_svg_support');
function add_svg_support($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}

//Create a short code for the editor that will insert another file
/*
function fetchForm( $atts ){
	ob_start();
	include(WP_CONTENT_DIR . '/themes/apre/inc/form.php');
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'insert_form', 'fetchForm' );
*/

//Custom Post Type
/*
function create_post_type() {
		  	register_post_type( 'NameOfPostType',
		    array(
			      'labels' => array(
				        'name' => __( 'Label Plural' ),
				        'singular_name' => __( 'Label Single' )
			      ),
			      'exclude_from_search' => true,
			      'public' => true,
			      'has_archive' => true,
			      'supports' => array('title','editor','thumbnail'),
			      //'menu_icon' => 'dashicons-admin-users',
			    )
	  );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_post_type' );
*/

//Example Breadcrumb nav
/*
function streamlineBreadcrumbs($post, $displayCurrent){
	  $count = 1;
	  $postAncestors = get_post_ancestors($post);
	  $sortedAncestorArray = array();
	  foreach ($postAncestors as $ancestor){
		    $sortedAncestorArray[] = $ancestor;
	  }
	  krsort($sortedAncestorArray); // Sort an array by key in reverse order
 
	  foreach ($sortedAncestorArray as $ancestor){
		    echo "<a class='crumb next' href='". esc_url(get_permalink($ancestor)) ."' title='". get_the_title($ancestor) ."'>";
		    echo '<span class="before"></span>';
		    echo "<span class='text'>";
		    echo get_the_title($ancestor);
		    echo "</span>";
		    echo "</a>";
		    $count++;
  	}
	  if($displayCurrent){ //If TRUE - output the current page title
		    echo "<span class='crumb active'>";
		    echo '<span class="before"></span>';
		    echo "<span class='text'>";
		    echo get_the_title($post);
		    echo "</span>";
		    echo '<span class="after"></span>';
		    echo "</span>";
	  }
}
*/

//Example Sidebar Nav using the meu system
/*
//Sidebar nav using the menu system
class Primary_Walker_Nav_Menu extends Walker_Nav_Menu {
	var $found_parents = array();
	
    function start_el(&$output, $item, $depth, $args) {
        global $wp_query;
		
        if( $wp_query->is_single && !in_array( get_option('page_for_posts'), $this->found_parents ) ) {
            $this->found_parents[] = get_option('page_for_posts');
        }

		
        //this only works for second level sub navigations
        $parent_item_id = 0;

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="'.esc_attr($class_names).'"';

        #current_page_item
        // Checks if the current element is in the current selection
        if(
            strpos($class_names, 'current-menu-item') || 
            strpos($class_names, 'current-menu-parent') || 
            strpos($class_names, 'current-menu-ancestor') || 
            ( is_array($this->found_parents) && 
                in_array($item->menu_item_parent, $this->found_parents) )
        ) {
            // Keep track of all selected parents
            $this->found_parents[] = $item->ID;
            //check if the item_parent matches the current item_parent
            $item_output = '';
            if ($item->menu_item_parent != $parent_item_id ) {
                $output .= $indent.'<li'.$class_names.'>';

                $attributes = !empty($item->attr_title) ? ' title="'.esc_attr($item->attr_title).'"' : '';
                $attributes .= !empty($item->target) ? ' target="'.esc_attr($item->target).'"' : '';
                $attributes .= !empty($item->xfn) ? ' rel="'.esc_attr($item->xfn).'"' : '';
                $attributes .= !empty($item->url) ? ' href="'.esc_attr($item->url).'"' : '';

                $item_output = $args->before;
				
				//page url
				$pageURl =  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$linkURL = $item->url;
				$activeClass = "";
				if($linkURL == $pageURl){
					$activeClass = "active";
				}

                $item_output .= "<a".$attributes." class='$activeClass'>";
                $item_output .= $args->link_before.apply_filters('the_title', $item->title, $item->ID).$args->link_after;
                $item_output .= '</a>';
                $item_output .= $args->after;
            }
            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }

    function end_el(&$output, $item, $depth) {
        $parent_item_id = 0;

        $class_names = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="'.esc_attr($class_names).'"';

        if(
            strpos($class_names, 'current-menu-item') || 
            strpos($class_names, 'current-menu-parent') || 
            strpos($class_names, 'current-menu-ancestor') || 
            (is_array($this->found_parents) && 
                in_array($item->menu_item_parent, $this->found_parents))
        ) {
            // Closes only the opened li
            if (is_array($this->found_parents) && in_array($item->ID, $this->found_parents) && $item->menu_item_parent != $parent_item_id) {
                $output .= "</li>\n";
            }
        }
    }

    function end_lvl(&$output, $depth) {
        $indent = str_repeat("\t", $depth);
        // If the sub-menu is empty, strip the opening tag, else closes it
        if (substr($output, -22) == "<ul class=\"sub-menu\">\n") {
            $output = substr($output, 0, strlen($output) - 23);
        } else {
            $output .= "$indent</ul>\n";
        }
    }
}
*/

//Customizer
require get_template_directory(). '/inc/customizer.php';

//Add Classes to the body tag
function wpc_body_classes( $classes ) {

    //Add Class for Template
    if(is_page_template()){
      $tmp = get_page_template_slug($post_id);
      $getSlug = str_replace("page-", "", $tmp);
      $getSlug = str_replace(".php", "", $getSlug);
      $classes[] = $getSlug;
    }

    return $classes;
}
add_filter( 'body_class', 'wpc_body_classes' );


//Turn on Revisions and set the number of revisons
define('WP_POST_REVISIONS', true);
define('WP_POST_REVISIONS', 3);
Basic Intial Setup of Theme
Style Sheet Path - style.css - note you can define these in the function file instead
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />

Additional style sheets or js files - /css/additional css - note should probably define in function
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/theme.css" type="text/css" />

HTML language attributes
<html <?php language_attributes(); ?>>

Charset
<meta charset="<?php bloginfo('charset'); ?>">

Get the Blog Description
<?php bloginfo('description'); ?>

Title Meta Tag - blog name | page title
<title>
  <?php bloginfo('name'); ?> | 
  <? is_front_page() ? bloginfo('description') : php wp_title(); ?>
</title>
Page head - adds wordpress other addons
  <?php wp_head(); ?>
</head>
Page footer - adds wordpress other addons
  <?php wp_footer(); ?>
</body>
Create your header (header.php) and footer (footer.php) files
<?php get_header(); ?>
<?php get_footer(); ?>
Copyright year and site name
<?php echo Date('Y'); ?> <?php bloginfo('name'); ?>

Menus
-Registering new menu containers can be done in the functions file
-After the contair has been created from the functions file it should be ready in WP to add nav to it
-Adding menu to page

//this will display the example nav location container from the functioms file
<?php wp_nav_men(array('theme_location' => 'newMenu')); ?>
WP Bootstrap v4 NavWalker
-Download PHP Class file from GitHub(jprieton) wp-bootstrap-navwalker.php and place it in the root of your theme.
-Update Functions file.

//Register Nav walker class
require_once get_template_directory() . '/wp-bootstrap-navwalker.php';
-Replace your navigation with

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNavDropdown">
<?php
  wp_nav_menu( array(
    'menu'              => 'primary',
    'theme_location'    => 'primary',
    'depth'             => 2,
    'container'         => 'div',
    'container_class'   => 'collapse navbar-collapse',
    'container_id'      => 'bs-example-navbar-collapse-1',
    'menu_class'        => 'nav navbar-nav',
    'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
    'walker'            => new WP_Bootstrap_Navwalker())
   );
?>
</div>
-In WP make sure menu and them location match the code above (menu = primary, theme_location = primary)

Example Breadcrumb Navigation
-Check out the function file for some example setup

<?php streamlineBreadcrumbs($post,true); ?>
Alternative to Walker to generate a side menu use wp_list_pages
<?php
$capMenu = wp_list_pages("title_li=&child_of=111&depth=3");
echo $capMenu;
?>
Example Sidbar Walker Navigation
-Check out the function file for some example setup

<?php
		//use walker to pull level navigation
		$sidebar1 = array(
				'theme_location'  => 'top',
				'container'       => 'ul',
				'menu_class'      => 'nav navbar-nav main-nav',
				'walker'          => new Primary_Walker_Nav_Menu()
		);
		wp_nav_menu( $sidebar1 );
	?>

	<?php
		//use walker to pull level navigation
		$sidebar2 = array(
				'theme_location'  => 'utility',
				'container'       => 'ul',
				'menu_class'      => 'nav navbar-nav main-nav',
				'walker'          => new Primary_Walker_Nav_Menu()
		);
		wp_nav_menu( $sidebar2 );
	?>
Widgets
-See Functions for refrence on Adding Widgets into WP and intiall config
-After functions file has been update add a widget to the container through WP.
-Add the following to display widgets in container

//Check for widgets - use id defined in functions file as the param in this example its 'sidebar'
<?php if(is_active_sidebar('sidebar')): ?>
  <?php dynamic_sidebar('sidebar'); ?>
<?php endif; ?>
-Another Example of pulling an additionl widget

//Param in this example its 'widgetc2'
<?php if(is_active_sidebar('widgetc2')): ?>
  <?php dynamic_sidebar('widgetc2'); ?>
<?php endif; ?>
Post Loop (index.php)- using template parts so post loop code is less repeated between templates
//check for posts
<?php if(have_posts()) : ?>
  
  //Loop through posts
  <?php while(have_posts()) : the_post(); ?>
    
    //include content file - see content.php example
    //also add the post fommat function
    <?php get_template_part('content', get_post_format()); ?>
  
  <?php endwhile; ?>
<? else: ?>
  <p><?php __('No Posts Found'); ?></p>
<? endif; ?>
Single Post Page (single.php) - essentially a modified version of the post loop
//check for posts
<?php if(have_posts()) : ?>
  
  //Loop through posts
  <?php while(have_posts()) : the_post(); ?>
 
    //include content file - see content.php example
    //also add the post fommat function
    <?php get_template_part('content', get_post_format()); ?> 

  <?php endwhile; ?>
<? else: ?>
  <p><?php __('No Posts Found'); ?></p>
<? endif; ?>
Custom Coments (comments.php)
-Note comments can be turned off in the settings area of WP

<div class="comments">
  <h2>Comments</h2>
  
  <?php $args = array(
    'walker'            => null,
    'max_depth'         => '',
    'style'             => 'ul',
    'callback'          => null,
    'end-callback'      => null,
    'type'              => 'all',
    'reply_text'        => 'Reply',
    'page'              => '',
    'per_page'          => '',
    'avatar_size'       => 32,
    'reverse_top_level' => null,
    'reverse_children'  => '',
    'format'            => 'html5', // or 'xhtml' if no 'HTML5' theme support
    'short_ping'        => false,   // @since 3.6
    'echo'              => true     // boolean, default is true
  ); ?>
  
  <?php wp_list_comments(array($args, $comments)); ?>
  
  <?php
    $comments_args = array(
          // change the title of send button 
          'label_submit'=>'Send',
          // change the title of the reply section
          'title_reply'=>'Write a Reply or Comment',
          // remove "Text or HTML to be displayed after the set of comment fields"
          'comment_notes_after' => '',
          // redefine your own textarea (the comment body)
          'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><br /><textarea id="comment" name="comment" aria-required="true"></textarea></p>',
  );

  comment_form($comments_args);

  ?>
  
  
</div>
Page (page.php) - essentially a modified version of the post loop
//check for posts
<?php if(have_posts()) : ?>
  
  //Loop through posts
  <?php while(have_posts()) : the_post(); ?>
    
    //include content file - see content.php example
    //also add the post fommat function
    <?php get_template_part('content', get_post_format()); ?>
  
  <?php endwhile; ?>
<? else: ?>
  <p><?php __('No Page Found'); ?></p>
<? endif; ?>
Archive (archive.php) - essentially a modified version of the post loop
//check for posts 
<?php if(have_posts()) : ?>
  
  //Archive Header
  <?php
    the_archive_title( '<h1>', '</h1>' );
    the_archive_description( '<div>', '</div>' );
  ?>
  
  //Loop through posts
  <?php while(have_posts()) : the_post(); ?>
    
    //include content file - see content.php example
    //also add the post fommat function
    <?php get_template_part('content', get_post_format()); ?>   
  
  <?php endwhile; ?>
<? else: ?>
  <p><?php __('No Posts Found'); ?></p>
<? endif; ?>
Content.php - contains conditional logic for other templates to follow
<h2>

  //if single post or page
  <?php if(is_single() || is_page()): ?>
    
    //Title
    <?php the_title(); ?>
  
  <?php else ?>
    
    //Link and Title
    <a href="?php the_permalink() ?>"><?php the_title(); ?></a>
  
  <?php endif; ?>

</h2>

//if single post
<?php if(is_single()) : ?>

  //Date with formatting
  <?php the_date('F j, Y g:i a'); ?>

  //Author Date with link to authors page
  <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>
  
<?php endif; ?>

//Add Post Image - check if image exists
<?php if(has_post_thumbnail()) : ?>
  //Display Image - Use HTML/CSS to control image size and layout
  <?php the_post_thumbnail(); ?>
<?php endif; ?>

//if single post or page
<?php if(is_single() || is_page()): ?>

  //Full content
  <?php the_content(); ?>

<?php else ?>

  //Excerpt - to limit characters see functions file 
  <?php the_excerpt(); ?>

<?php endif; ?>

//if single post
<?php if(is_single()): ?>

  //Add Comments - to customize the comments see comments.php code
  <?php comments_template(); ?>

<?php endif; ?>
Home Page / front-page.php - custom home page
<?php get_header(); ?>
  

<?php get_footer(); ?>
-Note: This may come in handy

//condition to check if not font-page
<?php if(!is_front_page()): ?>
  Not on Front Page
<?php else; ?>
  On Front Page
<?php endif; ?>

Post Formats - lets you define posts as something other than standard.
-This could allow you to do something different with posts that have been created with different formats
-You could also creat a custom post type as an alternative
-See Functions file for configuring WP to display them as an option
-Make sure get_template_part in you loop has get_post_format(), so post formats go to the right files
-In WP create a post with aside as an example
-Create a content-aside.php file (note aside in the file name will be different for other formats)

Aside example (content-aside.php)
<div class="aside">
  <?php the_author(); ?>
  <?php the_date('F j, Y g:i a'); ?>
  <?php the_content(); ?>
</div>
Gallery Example (content-gallery.php)
<div class="gallery">
  <h2><?php the _title(); ?></h2>
  <?php the_content(); ?>
</div>
Customizer
-Add code to Functions file to refrence customizer code
-Create folder directory structure '/inc/customizer.php'
-For the background image you'll want to make sure you have an image in the a foler named img/showcase.jpg

<?php
  function wpc_customizer_register($wp_customize){
    
    //Showcase Section
    $wp_customize->add_section('showcase', array(
      'title' => __('Showcase', 'wpc'), //title & theme name
      'description' => sprintf(__('Options for showcase', 'wpc')), //description and theme name
      'priority' =>  130
    ));
    
    //Custom Background Image Component
    $wp_customize->add_setting('showcase_image', array(
      'default' => get_bloginfo('template_directory').'/img/showcase.jpg', //default image and theme name
      'type' => 'theme_mod' //type of mod
    )); 
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'showcase_image', array(
      'label' => __('Background Image', 'wpc'), //Label for textbox  and theme name
      'section' => 'showcase',  //specify section
      'settings' => 'showcase_image',
      'priority' => 1 //order
    )));
    
    //Custom Heading Component
    $wp_customize->add_setting('showcase_heading', array(
      'default' => _x('Custom Heading', wpc), //default text and theme name
      'type' => 'theme_mod' //type of mod
    )); 
    $wp_customize->add_control('showcase_heading', array(
      'label' => __('Heading', 'wpc'), //Label for textbox  and theme name
      'section' => 'showcase',  //specify section
      'priority' => 2 //order
    ));
    
    //Custom Text Component
    $wp_customize->add_setting('showcase_text', array(
      'default' => _x('Custom Text', wpc), //default text and theme name
      'type' => 'theme_mod'
    ));
    $wp_customize->add_control('showcase_text', array(
      'label' => __('Text', 'wpc'), //Label for textbox  and theme name
      'section' => 'showcase',  //specify section
      'priority' => 3 //order
    ));
    
    //Custom Button Link Component
    $wp_customize->add_setting('btn_url', array(
      'default' => _x('http://google.com', wpc), //default text and theme name
      'type' => 'theme_mod'
    ));
    $wp_customize->add_control('btn_url', array(
      'label' => __('Button URL', 'wpc'), //Label for textbox  and theme name
      'section' => 'showcase',  //specify section
      'priority' => 4 //order
    ));
    
    //Custom Button Text Component
    $wp_customize->add_setting('btn_text', array(
      'default' => _x('Read More', wpc), //default text and theme name
      'type' => 'theme_mod'
    ));
    $wp_customize->add_control('btn_text', array(
      'label' => __('Button Text', 'wpc'), //Label for textbox  and theme name
      'section' => 'showcase',  //specify section
      'priority' => 5 //order
    ));
    
  }
  add_action('customize_register', 'wpc_customizer_register')
?>
-Rendering to Page example

<div style="background: url(<?php echo get_theme_mod('showcase_image', get_bloginfo('template_url).'/img/showcase.jpg')); ?>)">
  <h1><?php echo get_them_mod('showcase_heading', 'Default Text')></h1>
</div>
Search Page (search.php) - essentially the same as the post page


<?php if ( have_posts() ) : ?>
  <?php printf( __( 'Search Results for: %s', 'wpc' ), '<span>' . get_search_query() . '</span>' ); ?>
		<?php else : ?>
  <?php _e( 'Nothing Found', 'wpc' ); ?>
		<?php endif; ?>

//check for posts
<?php if(have_posts()) : ?>
  
  //Loop through posts
  <?php while(have_posts()) : the_post(); ?>
    
    //include content file - see content.php example
    //also add the post fommat function
    <?php get_template_part('content', get_post_format()); ?>
    
    <?php 
					/* Search Count */
					$allsearch =  new WP_Query("s=$s&showposts=-1");
					$key = wp_specialchars($s, 1);
					$count = $allsearch->post_count;
					echo $count;
					wp_reset_query();
					?>
  
  
  <?php endwhile; ?>
<? else: ?>
  
  <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wpc' ); ?></p>
  			<?php 				get_search_form(); 		endif; ?>
  
<? endif; ?>
Search Form (searcForm.php)
<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo $unique_id; ?>">
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'wpc' ); ?></span>
	</label>
	<input type="search" id="<?php echo $unique_id; ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'wpc' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="search-submit"><?php echo wpc_get_svg( array( 'icon' => 'search' ) ); ?><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'wpc' ); ?></span></button>
</form>
Using Jquery
-WP already incudes jquery library, so all you have to worry about is the ready function and using the $ in your own JS file

jQuery(document).ready(function($) {
    
    console.log($(".custom-logo"))

});
Page Templates
-Copy page.php and name it something like page-namehere.php
-Add the following into the header of the file.

<?php
  /*
  * Template Name: namehere
  */
?>
-Now that template name should appear as a selectable template for WP pages.
-Note a class that is added to the body from the functions file.

Share Pages - You could use a plugin or just create the code yourself
-Create a file named share-links.php in the template-parts folder

<?php
	$pageLink = esc_url( get_permalink() );
	$pageTitle = get_the_title();
	$twitterHandle = "";
	$emailSubject = "sub";
	$emailBody = "body";
?>

Share Links:
<a href="https://www.facebook.com/sharer/sharer.php?u=?<?php echo $pageLink; ?>">Facebook</a>
<a href="https://twitter.com/intent/tweet?text=<?php echo $pageTitle; ?>&url=<?php echo $pageLink; ?>&via=<?php echo $twitterHandle; ?>">Twitter</a>
<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $pageLink; ?>">LinkedIn</a>
<a href="mailto:?subject=<?php echo $emailSubject; ?>&body=<?php echo $emailBody; ?>">Email</a>
<a href="javascript:window.print();">Print</a>
-Then add it to the templates that you want the share buttons to show upl

<?php get_template_part( 'template-parts/share-links' ); ?>
Custom Post Types - Adds New sections besided the default Posts, Media & Pages
-See Functions file for example code

Short Code that you can add into the WYSIWG editor to include a PHP file
-See Functions File for configuration

[insert_form]
ACF (Free)
-Example of displaying a home slide items

<?php //Loop Through homeslides
				    $args = array( 'post_type' => 'homeslides', 'posts_per_page' => 100 );
					$loop = new WP_Query( $args );
					
					while ( $loop->have_posts() ) : $loop->the_post();
	
						$title = get_the_title();						
						$description = get_field("description");
						$link = get_field("link");
						$quote = get_field("quote");
						
						$noImgClass = "";
						
						if(!has_post_thumbnail()){
							$noImgClass = "green";
						}

						echo "<div class='round quarter_1 $noImgClass'>";
							
							if(has_post_thumbnail()){
								the_post_thumbnail( $size, $attr );
							}else{
								echo '<div class="quote">';
									echo '<span class="icon quotation"></span>';
									echo "<p>&quot;$quote&quot;</p>";
								echo '</div>';
							}
							echo '<div class="caption animate">';
								echo "<h2><a href='$link'>$title</a></h2>";
								echo '<div class="remainder">';
									echo '<a href="#">';
										echo "<p>$description</p>";
									echo '</a>';
								echo '</div>';
							echo '</div>';
						echo '</div>';

					endwhile;
				?>
-Example of displaying a list of videos from a custom Post Type and ACF

<div id="active_media" class="grid_9 omega video" style="margin-left: 0px;"></div>
		<div id='media' class="grid_9 omega masonry" style="margin-left: 0px; min-height:auto;">
		<?php if(have_rows("select_videos")):
			
			$i = 0;
			$activeClass = "";

			while( have_rows("select_videos") ) : the_row();

					if($i==0){
						$activeClass = "active";
					}else {
						$activeClass = "";
					}

					$video = get_sub_field("video");
						
						echo "<a style='' href='". get_field("large_img", $video->ID ) ."' class='media-resource animate ". $activeClass ."' id='" . $video->post_name . "' data-type='vimeo' data-resource='" . get_post_meta($video->ID, "video_id", true) . "' data-orig='". get_field("reg_img", $video->ID ) ."' 
						data-caption='White potatoes pack a powerful nutrition punch! In this APRE video, registered dietitian Bethany Thayer shares nutrition facts about this tasty and versatile vegetable. Visit APRE's Media Center to view more educational videos.' data-width='500' data-height='281'>
						<img src='". get_field("thumbnail_img", $video->ID ) ."' />
						<img class='vid_overlay' src='http://www.apre.org/images/video_overlay.png' /></a>";

					$i++;
					
				endwhile;
			endif;
		?>
		</div>