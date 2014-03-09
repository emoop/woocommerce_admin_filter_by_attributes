<?php
/**
 * Plugin Name: Woocommerce_admin_filter_by_attributes
 * Plugin URI: https://github.com/emoop/woocommerce_admin_filter_by_attributes
 * Description: Filtering variable product by custom attributes like "size" or  "colors" in admin product area.
 *
 * Author: emoop
 * Author URI:
 * Version: 1.0
 * Stable tag: 1.0
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
 
 if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
        return;
        
  //add drop down to admin products area		
 add_action('restrict_manage_posts','woo_attribute_form');
 
 //filter
 add_filter('the_posts','filter_by_custom_attribute');
 
 function woo_attribute_form(){
     global $woocommerce;
        //get custom attribute
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        $output = "<select name='attribute' id='dropdown_product_att'>";
        $output .="<option>attributes</option>";
		foreach ( $attribute_taxonomies as $attribute ) {
		   $terms=get_terms('pa_'.$attribute->attribute_name);
		   foreach($terms as $term){
		   $output .="<option name=pa_".$attribute->attribute_name.">". $term->name."</option>";
		   }
		}
 

 $output .="</select>";
 echo $output;
}
 
 
 
function filter_by_custom_attribute($posts){
  global $pagenow, $wpdb, $wp;

  
	        //'edit.php' != $pagenow  - only for admin
		if (  'edit.php' != $pagenow ||  'product' != $wp->query_vars['post_type'] || ! isset( $_GET['attribute']) ){
				return $posts;
			}
		
		 // get the atrribute param
		  $term=$_GET['attribute'];
		
			$product_ids = $wpdb->get_results(  "SELECT p.post_parent	FROM  `wp_posts` p 
			                           LEFT JOIN wp_postmeta pm  ON  pm.post_id = p.ID
			                           LEFT JOIN  wp_terms t  ON  pm.`meta_value` = t.slug
				                       WHERE t.name LIKE'".$term ."'" ) ;
            if($product_ids) $posts='';
         
		foreach($product_ids as $k=>$v){
		      
		     $posts[] = get_post($v->post_parent);
		}
		
		return $posts;
		
		
}
 
?>
