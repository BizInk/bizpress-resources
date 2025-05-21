<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'display_post_states', 'bizpress_resources_post_states', 10, 2 );
function bizpress_resources_post_states( $post_states, $post ) {
	$resourcesResourcesPageID = intval(cxbc_get_option( 'bizink-client_basic', 'resources_content_page' ));
    if ( $resourcesResourcesPageID == $post->ID ) {
        $post_states['bizpress_resources'] = __('BizPress Resources','bizink-resources');
    }

    return $post_states;
}

function resources_settings_fields( $fields, $section ) {
	$pageselect = false;
	if(defined('CXBPC')){
		$bizpress = get_plugin_data( CXBPC );
		$v = intval(str_replace('.','',$bizpress['Version']));
		if($v >= 151){
			$pageselect = true;
		}
	}
	
	if('bizink-client_basic' == $section['id']){
		$fields['resources_content_page'] = array(
			'id'      => 'resources_content_page',
			'label'     => __( 'Resources', 'bizink-resources' ),
			'type'      => $pageselect ? 'pageselect':'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-resources' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			'default_page' => [
				'post_title'   => __( 'Resources', 'bizink-resources' ),
				'post_content' => "[bizpress-content]",
				'post_status'  => "publish",
				'post_type'    => "page"
			],
			'required'	=> false,
		);
	}
	
	if('bizink-client_content' == $section['id']){
		$fields['resources_label'] = array(
			'id' => 'resources',
	        'label'	=> __( 'Bizpress Resources', 'bizink-resources' ),
	        'type' => 'divider'
		);
		$fields['resources_title'] = array(
			'id' => 'resources_title',
			'label'     => __( 'Resources Title', 'bizink-resources' ),
			'type'      => 'text',
			'default'   => __( 'Resources', 'bizink-resources' ),
			'required'	=> true,
		);
		$fields['resources_desc'] = array(
			'id'      	=> 'resources_desc',
			'label'     => __( 'Resources Description', 'bizink-resources' ),
			'type'      => 'textarea',
			'default'   => __( 'Free resources to help you with resources resources.', 'bizink-resources' ),
			'required'	=> false,
		);

	}

	return $fields;
}
add_filter( 'cx-settings-fields', 'resources_settings_fields', 10, 2 );

function resources_content( $types ) {
	$types[] = [
		'key' 	=> 'resources_content_page',
		'type'	=> 'resources'
	];
	return $types;
}
add_filter( 'bizink-content-types', 'resources_content' );

if( !function_exists( 'bizink_get_resources_page_object' ) ){
	function bizink_get_resources_page_object(){
		$post_id = cxbc_get_option( 'bizink-client_basic', 'resources_content_page' );
		$post = get_post( $post_id );
		return $post;
	}
}

add_action( 'init', 'bizink_resources_init');
function bizink_resources_init(){
	$post = bizink_get_resources_page_object();
	if( is_object( $post ) && get_post_type( $post ) == "page" ){
		add_rewrite_tag('%'.$post->post_name.'%', '([^&]+)', 'bizpress=');
		add_rewrite_tag('%'.$post->post_name.'%', '([^&]+)', 'resource=');
		add_rewrite_rule("^".$post->post_name."\/([a-z0-9-]+)[/]?$",'index.php?pagename=resources&resource=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."\/([a-z0-9-]+)\/([a-z0-9-]+)[/]?$",'index.php?pagename=resources&type=$matches[1]&bizpress=$matches[2]','top');

		//add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=resources&bizpress=$matches[1]','top');
		//add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename=resources&bizpress=$matches[1]','top');

		//add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)/([a-z0-9-]+)[/]?$" ,'index.php?pagename=resources&type=$matches[1]&topic=$matches[2]','top');

		//add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename=resources&topic=$matches[1]','top');
		//add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename=resources&type=$matches[1]','top');
		
		//flush_rewrite_rules();
	}
}

add_filter('query_vars', 'bizpress_resources_qurey');
function bizpress_resources_qurey($vars) {
    $vars[] = "bizpress";
	$vars[] = "resource";
	$vars[] = "bizpressxml";
    return $vars;
}

function bizpress_resources_sitemap_custom_items( $sitemap_custom_items ) {
    $sitemap_custom_items .= '
	<sitemap>
		<loc>'.get_home_url().'/resources.xml</loc>
	</sitemap>';
    return $sitemap_custom_items;
}

add_filter( 'wpseo_sitemap_index', 'bizpress_resources_sitemap_custom_items' );

function bizpress_resources_content_manager_fields($fields){
	$data = null;
	if(function_exists('bizink_get_content')){
		$data = bizink_get_content( 'resources', 'topics' );
	}
	$fields['resources'] = array(
		'id' => 'resources',
		'label'	=> __( 'Resources', 'bizink-client' ),
		'posts' => $data ? $data->posts : array(),
	);
	return $fields;
}
add_filter('bizpress_content_manager_fields','bizpress_resources_content_manager_fields');