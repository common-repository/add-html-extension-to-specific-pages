<?php
/*
Plugin Name: Add HTML Extension to Specific Pages
Plugin URI: http://www.cherryant.com
Description: Appends .html extension to the Page URL when using seo permalinks and page permalink box has _html text.
Version: 1.0
Author: CherryAnt.com
Author URI: http://www.cherryant.com
License: GPL2
*/
?>
<?php
register_activation_hook(__FILE__, 'active');
register_deactivation_hook(__FILE__, 'deactive');

function ahesp_user_trailingslashit($string, $type){
	global $wp_rewrite;
	
	if ($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
		if ( strpos($string, '_html/')){
			return untrailingslashit(str_replace('_html','.html',$string));
		}else{
			return $string;
		}
	}else{
		return $string;
	}
}
add_filter('user_trailingslashit', 'ahesp_user_trailingslashit',66,2);

function ahesp_do_parse_request($continue, $wp, $extra_query_vars){
	if($continue && !is_admin() && strpos($_SERVER['REQUEST_URI'],'.html')){
		$baseurl = str_replace("index.php","",$_SERVER['PHP_SELF']);
		$url_path = trim(str_replace($baseurl,"",str_replace('.html','_html',$_SERVER['REQUEST_URI'])),'/');
		$url_path = sanitize_title_with_dashes($url_path);
	
		$query = new WP_Query(array(
			'name' => $url_path,
			'post_type' => 'page',
			'post_status' => 'publish'
		));

		if(1==$query->found_posts){
			$wp->query_vars = array(
				'pagename'	=> str_replace('.html','',$url_path),
				'page'		=> ''
			);
			$continue = false;
		}
	}
	return $continue;
}
add_filter('do_parse_request', 'ahesp_do_parse_request',10,3);

function ahesp_redirect_canonical($redirect_url, $requested_url){
	if(strpos($redirect_url,'.html/') && strpos($requested_url,'.html')){
		return false;
	}
}
add_action('redirect_canonical','ahesp_redirect_canonical',10,2);

function active() {
}	

function deactive() {
}
?>