<?php 

/* Goods post type*/

function post_type_goods() {
register_post_type(
                    'goods', 
                    array( 'public' => true,
					 		'publicly_queryable' => true,
							'has_archive' => true, 
							'hierarchical' => false,
							'menu_icon' => get_stylesheet_directory_uri() . '/images/box.png',
                    		'labels'=>array(
    									'name' => _x('商品', 'post type general name'),
    									'singular_name' => _x('商品', 'post type singular name'),
    									'add_new' => _x('添加新商品', 'Goods'),
    									'add_new_item' => __('添加新商品'),
    									'edit_item' => __('编辑商品'),
    									'new_item' => __('新的商品'),
    									'view_item' => __('浏览商品'),
    									'search_items' => __('搜索商品'),
    									'not_found' =>  __('没有找到商品'),
    									'not_found_in_trash' => __('在回收站未找到商品'), 
    									'parent_item_colon' => ''
  										),							 
                            'show_ui' => true,
							'menu_position'=>5,
							'query_var' => true,
							'rewrite' => true,
							'rewrite' => array( 'slug' => 'item', 'with_front' => FALSE,),
							'register_meta_box_cb' => 'mytheme_add_box',
							'supports' => array(
							 			'title',
										'thumbnail',
										'custom-fields',
										'comments',
										'editor'
										)
							) 
					);
				} 
add_action('init', 'post_type_goods');

/* Movie genre taxonomy */

function create_products_taxonomy() 
{
$labels = array(
	  						  'name' => _x( '商品类型', 'taxonomy general name' ),
    						  'singular_name' => _x( '商品类型', 'taxonomy singular name' ),
    						  'search_items' =>  __( '搜索商品类型' ),
   							  'all_items' => __( '所有的商品类型' ),
    						  'parent_item' => __( '父级商品类型' ),
   					   		  'parent_item_colon' => __( '父级商品类型：' ),
   							  'edit_item' => __( '编辑商品类型' ), 
  							  'update_item' => __( '更新商品类型' ),
  							  'add_new_item' => __( '添加新的商品类型' ),
  							  'new_item_name' => __( '新商品类型的名称' ),
); 	
register_taxonomy('product',array('goods'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'product' ),
  ));
}

add_action( 'init', 'create_products_taxonomy', 0 );

/* ADD FEATURED TERM */

function add_product_term_featured() {
if(!is_term('Featured', 'product')){
  wp_insert_term('Featured', 'product');
}
}
add_action( 'init', 'add_product_term_featured' );

?>