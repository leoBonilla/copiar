<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,
	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';
	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
		require 'inc/nux/class-storefront-nux-starter-content.php';
	}
}

// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page' );
}

load_theme_textdomain( 'mindwaytheme' ); //TODO

if ( function_exists( 'add_theme_support' ) )
add_theme_support( 'post-thumbnails' );

add_action( 'init', 'register_my_menus' );
function register_my_menus() {
	register_nav_menus(
		array(
		'menu-1' => __( 'Menu Principal' ),
		'menu-2' => __( 'Menu Footer' )
		)
	);
}
//add_filter( 'show_admin_bar', '__return_false' );

/* Agrega logo y redireccion a home en login dashboard*/
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
		background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/public/images/header/logo-color.svg);
        height: 30px;
        width: 321px;
        background-size: 191px 61px;
        background-repeat: no-repeat;
        padding-bottom: 30px;
    /* background-color: #243233; */
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Zenbyte';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );
/*FIN agrega logo y redireccion a home en login dashboard*/

//agrega variable para conectar con ajax al backend de wordpress
add_action('wp_head', 'myplugin_ajaxurl');
function myplugin_ajaxurl() {
    echo '<script type="text/javascript">
        var ajaxurl="'.admin_url('admin-ajax.php') . '";
    </script>';
}
//add_filter( 'show_admin_bar', '__return_false' );
//Agrega bootstrap js y css y custom.js
//wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.css',false,'1.1','all');
//wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/assets/css/custom.css',false,'1.1','all');
//wp_enqueue_script( 'bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js', array ( 'jquery' ), 1.1, true);
//wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array ( 'jquery' ), 1.1, true);

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
    return 22;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );


add_filter( 'wp_nav_menu_objects', 'add_menu_parent_class' );
function add_menu_parent_class( $items ) {
    $parents = array();
    foreach ( $items as $item ) {
        //Check if the item is a parent item
        if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
            $parents[] = $item->menu_item_parent;
        }
    }
    foreach ( $items as $item ) {
        if ( in_array( $item->ID, $parents ) ) {
            //Add "menu-parent-item" class to parents
            $item->classes[] = 'menu-parent-item'; 
        }
    }
    return $items;
}
/* 
**difiere el parseo de js, siempre al final del archivo
*/
if( is_admin() ) {
	add_filter( 'script_loader_tag', 'tp_theme_filter_overrides' , 10, 4 );
}

function tp_theme_filter_overrides( $tag, $handle ) {
	return str_replace( 'defer', '', $tag );
}
function defer_parsing_of_js ( $url ) {
	if ( FALSE === strpos( $url, '.js' ) ) return $url;
	if ( strpos( $url, 'jquery.js' ) ) return $url;
	return "$url' defer ";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
/*
**FIn defer js
*/

function add_opengraph_doctype($output) {
    return $output . '
    xmlns="https://www.w3.org/1999/xhtml"
    xmlns:og="https://ogp.me/ns#" 
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'add_opengraph_doctype');

//Add Open Graph Meta Info from the actual article data, or customize as necessary
	function facebook_open_graph() {
	    global $post;
	    if ( !is_singular()) //if it is not a post or a page
	        return;
		if($excerpt = $post->post_excerpt) 
	        {
			$excerpt = strip_tags($post->post_excerpt);
			$excerpt = str_replace("", "'", $excerpt);
        	} 
        	else 
        	{
            	$excerpt = get_bloginfo('description');
		}
			//You'll need to find you Facebook profile Id and add it as the admin
			
	        echo '<meta property="fb:admins" content="309388395845381-fb-admin-id"/>';
	        echo '<meta property="og:title" content="Econopticas"/>';
			echo '<meta property="og:description" content="' . $excerpt . '"/>';
	        echo '<meta property="og:type" content="article"/>';
	        echo '<meta property="og:url" content="' . get_permalink() . '"/>';
	        //Let's also add some Twitter related meta data
	        echo '<meta name="twitter:card" content="summary" />';
	        //This is the site Twitter @username to be used at the footer of the card
			echo '<meta name="twitter:site" content="@site_user_name" />';
			//This the Twitter @username which is the creator / author of the article
			echo '<meta name="twitter:creator" content="@username_author" />';
	        
	        // Customize the below with the name of your site
	        echo '<meta property="og:site_name" content="Econopticas"/>';
	        if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
	        //Create a default image on your server or an image in your media library, and insert it's URL here
	        $default_image="<?php echo get_template_directory_uri(); ?>/public/images/shareyoga.jpg' />"; 
	        echo '<meta property="og:image" content="' . $default_image . '"/>';
	    }
	    else{
	        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
	        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
	    }
	    echo "
	";
	}
add_action( 'wp_head', 'facebook_open_graph', 5 );

// Mostrar numero específico de categorías
add_filter('pre_get_posts', 'limit_category_posts');
function limit_category_posts($query){
    if ($query->is_category) {
        $query->set('posts_per_page', -1);
    }
    return $query;
}

function getInfoSquares() {
	wp_reset_query();
	$squares=array();
	query_posts(array(
		'post_type' => 'accesos_home',
		'posts_per_page' => 3
	));
	if (have_posts()):
		$i=0;
		while (have_posts()):the_post();
			$square=new stdClass();
			$square->image   = get_field('imagen_de_fondo');
			$square->text    = get_field('texto_cuadro');
			$square->link    = get_field('link_cuadro');
			$square->color   = get_field('color');
			// Inside Info
			$squares[]=$square;
			endwhile;
		endif;
	return $squares;
}

// BREADCRUMB  
function get_breadcrumb() {
    echo '<a href="/blog/" class="link-bread" rel="nofollow">Blog</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp; <i class='fas fa-caret-right'></i> &nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) {
				echo "&nbsp;&nbsp;<i class='fas fa-caret-right'></i>&nbsp;&nbsp;";
				?>
				<span class="current-bread"><?php the_title(); ?></span>
				<?php
            }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

// Videos responsive autómaticos
if(!function_exists('video_content_filter')) {
	function video_content_filter($content) {

    	// busca algún iFrame en la página
	$pattern = '/<iframe.*?src=".*?(vimeo|youtu\.?be).*?".*?<\/iframe>/';
	preg_match_all($pattern, $content, $matches);

	foreach ($matches[0] as $match) {
	// iFrame encontrado, ahora lo envolvemos en un DIV ...
	$wrappedframe = '<div class="flex-video">' . $match . '</div>';

	// Intercambia el original con el video, ahora encerrado
	$content = str_replace($match, $wrappedframe, $content);
	}
	return $content;
	}
	// Aplicar a areas de contenido de la página o entrada 
	add_filter( 'the_content', 'video_content_filter' );

	// Aplicar a los widgets si se quiere
	add_filter( 'widget_text', 'video_content_filter' );
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}

// add_action( 'wp_ajax_my_ajax_request', 'tft_handle_ajax_request' );
// add_action( 'wp_ajax_nopriv_my_ajax_request', 'tft_handle_ajax_request' );
//   function tft_handle_ajax_request() {
// 	$name	= isset($_POST['name'])?trim(strtolower($_POST['name'])):"";
// 	$query	= isset($_POST['query'])?trim(strtolower($_POST['query'])):"";
// 	$page	= isset($_POST['page'])?trim(strtolower($_POST['page'])):"";
// 	$filter	= array_unique(isset($_POST['filter'])?$_POST['filter']:"");
// 	$cat = "";
// 	$cats = array();
// 	foreach($filter as $item){
// 		$cat .= $item."+";
// 	}
// 	$cat = strtolower($cat);
// 	$cat = substr(trim($cat), 0, -1);
// 	$response	= array();
// 	$args = array( 'post_type' => 'product', 'posts_per_page' => 6, 'paged' => $page , 'order' => 'ASC');
// 	if($query != 'all'){
// 		 $args['product_cat'] = $cat;
// 	}
// 	$loop = new WP_Query( $args );
//      $html = '';
// 	while ( $loop->have_posts() ){
// 		$loop->the_post(); 
// 		global $product; 
// 		$html = $html. '<div class="col-md-4"><div class="product-item">';
// 		if (has_post_thumbnail( $loop->post->ID )){
// 			$html = $html . get_the_post_thumbnail($loop->post->ID, 'shop_catalog');
// 		}else{
// 			$html = $html . '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />';
// 		}
// 		$html = $html. '<h3 class="text-center">'. the_title('','', false).'</h3><span class="price">'.$product->get_price_html().'</span>';
//         $html = $html . '</div></div>';
// 	}
//    $pagination = '<div class="row"><div class="col-md-12"><div id="wp_pagination">
//    <a class="first page button" data-query="'.$cat.'" data-page="'.get_pagenum_link(1).'" href="'.get_pagenum_link(1).'">&laquo;</a>
//    <a class="previous page button" data-query="'.$cat.'" data-page="'.($curpage-1 > 0 ? $curpage-1 : 1).'" href="'.get_pagenum_link(($curpage-1 > 0 ? $curpage-1 : 1)).'">&lsaquo;</a>';
//    for($i=1;$i<=$loop->max_num_pages;$i++)
// 	   $pagination .= '<a  data-query="'.$cat.'" class="'.($i == $curpage ? 'active ' : '').'page button" data-page="'.$i.'" href="'.get_pagenum_link($i).'">'.$i.'</a>';
//        $pagination .= '
//    <a class="next page button" data-query="'.$cat.'" data-page="'.($curpage+1 <= $loop->max_num_pages ? $curpage+1 : $loop->max_num_pages).'" href="'.get_pagenum_link(($curpage+1 <= $loop->max_num_pages ? $curpage+1 : $loop->max_num_pages)).'">&rsaquo;</a>
//    <a class="last page button" data-query="'.$cat.'" data-page="'.$loop->max_num_pages.'" href="'.get_pagenum_link($loop->max_num_pages).'">&raquo;</a>
// </div></div></div>';
//      wp_reset_query();
// 	$response['message']= "Successfull Request ".$cat;
// 	$response['html'] = $html;
// 	$response['pagination'] = $pagination;
// 	$response['query'] = $query;
// 	$response['filters'] = $cat;
// 	echo json_encode($response);
//     exit;
//   }

add_action( 'wp_ajax_my_ajax_request', 'tft_handle_ajax_request' );
add_action( 'wp_ajax_nopriv_my_ajax_request', 'tft_handle_ajax_request' );
  function tft_handle_ajax_request() {
	$page	= isset($_POST['page'])?trim(strtolower($_POST['page'])):"";
	$showbrands	= (isset($_POST['brands'])  && $_POST['brands'] == 'true')? true :false;
	$number	= isset($_POST['number'])?trim(strtolower($_POST['number'])):"";
	$filter_cat = isset($_POST['filters']['filter_cat'])?trim(strtolower($_POST['filters']['filter_cat'])):false;
	$filter_type = isset($_POST['filters']['filter_type'])?trim(strtolower($_POST['filters']['filter_type'])):false;
	$filter_brand = isset($_POST['filters']['filter_brand'])?trim(strtolower($_POST['filters']['filter_brand'])):false;
	$filter_cat = strtolower(str_replace(' ', '-', $filter_cat));
	$filter_type = strtolower(str_replace(' ', '-', $filter_type));
	$filter_brand = strtolower(str_replace(' ', '-', $filter_brand));
	$response	= array();
	$args = array( 'post_type' => 'product', 'posts_per_page' => $number, 'paged' => $page , 'order' => 'ASC');
	$loop = new WP_Query( $args );
	if($filter_cat =="" && $filter_type== ""){
		//echo 'sin filtros';
	}elseif($filter_cat != "" || $filter_type != ""){
		 $query_search = $filter_cat.'+'. $filter_type;
		 $query_search = rtrim($query_search,'+');
		 $query_search = ltrim($query_search, '+');
		 $args['product_cat'] = $query_search;

	}
	if($filter_brand != ""){
		$args['tax_query'] = array(
      'taxonomy' => 'product_brands', //brands are terms of 'pwb-brand' taxonomy
      'field'    => 'name', //search by term name
      'terms'    => array ($filter_brand ) //brand names here
    );
	}
	$loop = new WP_Query( $args );
	$html = '';
	$terms = array();
	while ( $loop->have_posts() ){
		$loop->the_post(); 
		global $product;
		$prodid = $loop->post->ID;
		$brand = wp_get_post_terms( $prodid, 'product_brands', array('orderby'=>'name'));
		foreach ($brand as $value) {
			$terms[] = $value->name;
		}
		$html = $html. '<div class="col-md-4"><div class="product-item">';
		if (has_post_thumbnail( $loop->post->ID )){
			$html = $html . get_the_post_thumbnail($loop->post->ID, 'shop_catalog');
		}else{
			$html = $html . '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />';
		}
		$html = $html. '<h3 class="text-center">'. the_title('','', false).'</h3><span class="price">'.$product->get_price_html().'</span>';
        $html = $html . '</div></div>';
	}

	   $pagination = '<div class="row"><div class="col-md-12"><div id="wp_pagination">
   <a class="first page button" data-brand="" data-query="'.$query_search.'" data-page="'.get_pagenum_link(1).'" href="'.get_pagenum_link(1).'">&laquo;</a>
   <a class="previous page button" data-brand="" data-query="'.$query_search.'" data-page="'.($curpage-1 > 0 ? $curpage-1 : 1).'" href="'.get_pagenum_link(($curpage-1 > 0 ? $curpage-1 : 1)).'">&lsaquo;</a>';
   for($i=1;$i<=$loop->max_num_pages;$i++)
	   $pagination .= '<a  data-query="'.$query_search.'" class="'.($i == $curpage ? 'active ' : '').'page button" data-brand="" data-page="'.$i.'" href="'.get_pagenum_link($i).'">'.$i.'</a>';
       $pagination .= '
   <a class="next page button" data-brand="" data-query="'.$query_search.'" data-page="'.($curpage+1 <= $loop->max_num_pages ? $curpage+1 : $loop->max_num_pages).'" href="'.get_pagenum_link(($curpage+1 <= $loop->max_num_pages ? $curpage+1 : $loop->max_num_pages)).'">&rsaquo;</a>
   <a class="last page button" data-brand="" data-query="'.$query_search.'" data-page="'.$loop->max_num_pages.'" href="'.get_pagenum_link($loop->max_num_pages).'">&raquo;</a>
</div></div></div>';
    $brandstring = '';
    foreach ($terms as $value){
    	$brandstring .= '<li><a href="#">'.$value.'</a></li>';
    } 
    
    wp_reset_query();
	$response['html'] = $html;
	$response['pagination'] = $pagination;
	if($showbrands){
		$response['brandstring'] = $brandstring;
	}
	echo json_encode($response);
	exit();
  }